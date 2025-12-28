/**
 * Form Validation Script - Khó bypass
 * Sử dụng nhiều lớp validation và kỹ thuật chống bypass
 */
(function() {
    'use strict';
    
    // Lưu trữ các hàm validation để khó bị override
    const ValidationManager = (function() {
        const validators = new Map();
        const formConfigs = new Map();
        
        // Hàm validate email
        function validateEmail(email) {
            const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }
        
        // Hàm validate số điện thoại (tùy chọn)
        function validatePhone(phone) {
            if (!phone) return true; // Tùy chọn
            const re = /^[0-9]{10,11}$/;
            return re.test(phone.replace(/\s+/g, ''));
        }
        
        // Hàm validate số
        function validateNumber(value, min, max) {
            const num = parseInt(value);
            if (isNaN(num)) return false;
            if (min !== undefined && num < min) return false;
            if (max !== undefined && num > max) return false;
            return true;
        }
        
        // Hiển thị lỗi
        function showError(input, message) {
            const formGroup = input.closest('.form-group');
            if (!formGroup) return;
            
            // Xóa lỗi cũ
            const oldError = formGroup.querySelector('.error-message');
            if (oldError) oldError.remove();
            
            // Xóa class error cũ
            input.classList.remove('error');
            
            // Thêm lỗi mới
            if (message) {
                input.classList.add('error');
                const errorDiv = document.createElement('div');
                errorDiv.className = 'error-message';
                errorDiv.textContent = message;
                errorDiv.style.color = '#dc3545';
                errorDiv.style.fontSize = '12px';
                errorDiv.style.marginTop = '5px';
                formGroup.appendChild(errorDiv);
            }
        }
        
        // Validate một field
        function validateField(input, rules) {
            const value = input.value.trim();
            const type = input.type;
            const tagName = input.tagName.toLowerCase();
            
            // Kiểm tra required
            if (rules.required && !value) {
                showError(input, rules.requiredMessage || 'Trường này không được để trống');
                return false;
            }
            
            // Nếu không required và rỗng, bỏ qua các validation khác
            if (!rules.required && !value) {
                showError(input, '');
                return true;
            }
            
            // Validate email
            if (rules.email && !validateEmail(value)) {
                showError(input, rules.emailMessage || 'Email không hợp lệ');
                return false;
            }
            
            // Validate phone
            if (rules.phone && !validatePhone(value)) {
                showError(input, rules.phoneMessage || 'Số điện thoại không hợp lệ (10-11 số)');
                return false;
            }
            
            // Validate number
            if (rules.number) {
                if (!validateNumber(value, rules.min, rules.max)) {
                    let msg = 'Giá trị không hợp lệ';
                    if (rules.min !== undefined && rules.max !== undefined) {
                        msg = `Giá trị phải từ ${rules.min} đến ${rules.max}`;
                    } else if (rules.min !== undefined) {
                        msg = `Giá trị phải lớn hơn hoặc bằng ${rules.min}`;
                    } else if (rules.max !== undefined) {
                        msg = `Giá trị phải nhỏ hơn hoặc bằng ${rules.max}`;
                    }
                    showError(input, msg);
                    return false;
                }
            }
            
            // Validate min length
            if (rules.minLength && value.length < rules.minLength) {
                showError(input, rules.minLengthMessage || `Tối thiểu ${rules.minLength} ký tự`);
                return false;
            }
            
            // Validate max length
            if (rules.maxLength && value.length > rules.maxLength) {
                showError(input, rules.maxLengthMessage || `Tối đa ${rules.maxLength} ký tự`);
                return false;
            }
            
            // Validate password (nếu là form thêm mới)
            if (rules.password && value) {
                if (value.length < 6) {
                    showError(input, 'Mật khẩu phải có ít nhất 6 ký tự');
                    return false;
                }
            }
            
            // Nếu pass tất cả validation
            showError(input, '');
            return true;
        }
        
        // Validate toàn bộ form
        function validateForm(form) {
            const config = formConfigs.get(form);
            if (!config) return true;
            
            let isValid = true;
            const fields = config.fields;
            
            // Validate từng field
            for (const [fieldName, rules] of Object.entries(fields)) {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input && !validateField(input, rules)) {
                    isValid = false;
                }
            }
            
            return isValid;
        }
        
        // Đăng ký form
        function registerForm(form, config) {
            if (!form || !config) return;
            
            formConfigs.set(form, config);
            
            // Validate khi submit
            form.addEventListener('submit', function(e) {
                // Validate nhiều lần để khó bypass
                let valid = true;
                for (let i = 0; i < 3; i++) {
                    if (!validateForm(form)) {
                        valid = false;
                    }
                }
                
                if (!valid) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    // Focus vào field đầu tiên có lỗi
                    const firstError = form.querySelector('.error');
                    if (firstError) {
                        firstError.focus();
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                    
                    return false;
                }
            }, true); // Sử dụng capture phase để chạy trước các handler khác
            
            // Validate real-time khi blur
            for (const fieldName of Object.keys(config.fields)) {
                const input = form.querySelector(`[name="${fieldName}"]`);
                if (input) {
                    // Validate khi blur
                    input.addEventListener('blur', function() {
                        const rules = config.fields[fieldName];
                        validateField(input, rules);
                    });
                    
                    // Validate khi change (cho select)
                    if (input.tagName.toLowerCase() === 'select') {
                        input.addEventListener('change', function() {
                            const rules = config.fields[fieldName];
                            validateField(input, rules);
                        });
                    }
                    
                    // Validate khi input (cho text, email, number)
                    if (['text', 'email', 'number', 'password'].includes(input.type)) {
                        input.addEventListener('input', function() {
                            // Chỉ validate nếu đã blur một lần (có class error)
                            if (input.classList.contains('error')) {
                                const rules = config.fields[fieldName];
                                validateField(input, rules);
                            }
                        });
                    }
                }
            }
            
            // Thêm style cho error
            if (!document.getElementById('validation-styles')) {
                const style = document.createElement('style');
                style.id = 'validation-styles';
                style.textContent = `
                    .form-control.error {
                        border-color: #dc3545 !important;
                        box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
                    }
                `;
                document.head.appendChild(style);
            }
        }
        
        return {
            register: registerForm,
            validate: validateForm
        };
    })();
    
    // Export ra window với tên khó đoán
    window.__FV = ValidationManager;
    
    // Auto-init khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initValidation);
    } else {
        initValidation();
    }
    
    function initValidation() {
        // Tìm tất cả form có class 'validate-form'
        const forms = document.querySelectorAll('form.validate-form');
        forms.forEach(form => {
            const formId = form.id || form.getAttribute('data-form-id');
            if (formId && window.__FORM_CONFIGS && window.__FORM_CONFIGS[formId]) {
                ValidationManager.register(form, window.__FORM_CONFIGS[formId]);
            }
        });
    }
    
    // Bảo vệ khỏi việc override
    Object.freeze(ValidationManager);
})();

