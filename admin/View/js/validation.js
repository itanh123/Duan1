/**
 * Form Validation System - Khó bypass hơn
 * Sử dụng nhiều lớp bảo vệ để làm khó việc bypass validation
 */

(function () {
  "use strict";

  // Tạo một object bảo vệ để lưu trữ validation state
  const ValidationState = (function () {
    let state = {
      isValid: false,
      validated: false,
      timestamp: Date.now(),
    };

    return {
      setValid: function (value) {
        state.isValid = value;
        state.validated = true;
        state.timestamp = Date.now();
      },
      getValid: function () {
        // Kiểm tra timestamp để tránh replay attack
        if (Date.now() - state.timestamp > 300000) {
          // 5 phút
          state.validated = false;
        }
        return state.isValid && state.validated;
      },
      reset: function () {
        state.isValid = false;
        state.validated = false;
        state.timestamp = Date.now();
      },
    };
  })();

  // Freeze object để khó modify hơn
  Object.freeze(ValidationState);

  /**
   * Validate email format
   */
  function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  /**
   * Validate phone number (Vietnamese format)
   */
  function validatePhone(phone) {
    if (!phone) return true; // Optional field
    const phoneRegex = /^(0|\+84)[0-9]{9,10}$/;
    return phoneRegex.test(phone.replace(/\s/g, ""));
  }

  /**
   * Validate required field
   */
  function validateRequired(value) {
    return value !== null && value !== undefined && String(value).trim() !== "";
  }

  /**
   * Validate number range
   */
  function validateNumberRange(value, min, max) {
    const num = parseInt(value);
    return !isNaN(num) && num >= min && num <= max;
  }

  /**
   * Show error message
   */
  function showError(field, message) {
    // Remove existing error
    const existingError = field.parentElement.querySelector(".error-message");
    if (existingError) {
      existingError.remove();
    }

    // Add error class
    field.classList.add("error");

    // Create error message
    const errorDiv = document.createElement("div");
    errorDiv.className = "error-message";
    errorDiv.style.color = "#dc3545";
    errorDiv.style.fontSize = "12px";
    errorDiv.style.marginTop = "5px";
    errorDiv.textContent = message;
    field.parentElement.appendChild(errorDiv);
  }

  /**
   * Remove error message
   */
  function removeError(field) {
    field.classList.remove("error");
    const errorDiv = field.parentElement.querySelector(".error-message");
    if (errorDiv) {
      errorDiv.remove();
    }
  }

  /**
   * Validate form based on rules
   */
  function validateForm(form, rules) {
    let isValid = true;

    // Reset validation state
    ValidationState.reset();

    // Validate each field
    for (const fieldName in rules) {
      const field = form.querySelector(`[name="${fieldName}"]`);
      if (!field) continue;

      const value = field.value.trim();
      const fieldRules = rules[fieldName];

      // Required validation
      if (fieldRules.required && !validateRequired(value)) {
        showError(
          field,
          fieldRules.requiredMessage ||
            `${fieldRules.label || fieldName} không được để trống`
        );
        isValid = false;
        continue;
      }

      // Skip other validations if field is empty and not required
      if (!validateRequired(value) && !fieldRules.required) {
        removeError(field);
        continue;
      }

      // Email validation
      if (fieldRules.email && !validateEmail(value)) {
        showError(field, fieldRules.emailMessage || "Email không hợp lệ");
        isValid = false;
        continue;
      }

      // Phone validation
      if (fieldRules.phone && !validatePhone(value)) {
        showError(
          field,
          fieldRules.phoneMessage || "Số điện thoại không hợp lệ"
        );
        isValid = false;
        continue;
      }

      // Number range validation
      if (fieldRules.min !== undefined || fieldRules.max !== undefined) {
        const min = fieldRules.min !== undefined ? fieldRules.min : -Infinity;
        const max = fieldRules.max !== undefined ? fieldRules.max : Infinity;
        if (!validateNumberRange(value, min, max)) {
          showError(
            field,
            fieldRules.rangeMessage || `Giá trị phải từ ${min} đến ${max}`
          );
          isValid = false;
          continue;
        }
      }

      // Min length validation
      if (fieldRules.minLength && value.length < fieldRules.minLength) {
        showError(
          field,
          fieldRules.minLengthMessage ||
            `Tối thiểu ${fieldRules.minLength} ký tự`
        );
        isValid = false;
        continue;
      }

      // Max length validation
      if (fieldRules.maxLength && value.length > fieldRules.maxLength) {
        showError(
          field,
          fieldRules.maxLengthMessage || `Tối đa ${fieldRules.maxLength} ký tự`
        );
        isValid = false;
        continue;
      }

      // Custom validation function
      if (fieldRules.custom && typeof fieldRules.custom === "function") {
        const customResult = fieldRules.custom(value, form);
        if (customResult !== true) {
          showError(
            field,
            typeof customResult === "string"
              ? customResult
              : "Giá trị không hợp lệ"
          );
          isValid = false;
          continue;
        }
      }

      // Remove error if all validations pass
      removeError(field);
    }

    // Set validation state
    ValidationState.setValid(isValid);

    return isValid;
  }

  /**
   * Initialize form validation
   */
  function initFormValidation(formSelector, rules) {
    const form = document.querySelector(formSelector);
    if (!form) return;

    // Add CSS for error state
    if (!document.querySelector("#validation-styles")) {
      const style = document.createElement("style");
      style.id = "validation-styles";
      style.textContent = `
                .form-control.error {
                    border-color: #dc3545 !important;
                    box-shadow: 0 0 0 3px rgba(220, 53, 69, 0.1) !important;
                }
            `;
      document.head.appendChild(style);
    }

    // Real-time validation on blur
    for (const fieldName in rules) {
      const field = form.querySelector(`[name="${fieldName}"]`);
      if (field) {
        field.addEventListener("blur", function () {
          const fieldRules = {};
          fieldRules[fieldName] = rules[fieldName];
          validateForm(form, fieldRules);
        });

        // Remove error on input
        field.addEventListener("input", function () {
          if (this.classList.contains("error")) {
            const fieldRules = {};
            fieldRules[fieldName] = rules[fieldName];
            const value = this.value.trim();
            const fieldRule = rules[fieldName];

            // Quick check if field is now valid
            if (fieldRule.required && !validateRequired(value)) {
              return; // Still invalid
            }

            if (fieldRule.email && value && !validateEmail(value)) {
              return; // Still invalid
            }

            if (fieldRule.phone && value && !validatePhone(value)) {
              return; // Still invalid
            }

            removeError(this);
          }
        });
      }
    }

    // Form submit validation
    form.addEventListener("submit", function (e) {
      // Prevent default submission
      e.preventDefault();
      e.stopPropagation();

      // Validate form
      if (!validateForm(form, rules)) {
        // Scroll to first error
        const firstError = form.querySelector(".error");
        if (firstError) {
          firstError.scrollIntoView({ behavior: "smooth", block: "center" });
          firstError.focus();
        }
        return false;
      }

      // Double check validation state (khó bypass hơn)
      if (!ValidationState.getValid()) {
        alert("Form chưa được validate. Vui lòng thử lại.");
        ValidationState.reset();
        return false;
      }

      // If validation passes, submit form
      // Create a new form submission without validation
      const formData = new FormData(form);
      const action = form.getAttribute('action');
      const method = form.getAttribute('method') || 'POST';
      
      // Create a temporary form to submit
      const tempForm = document.createElement('form');
      tempForm.method = method;
      tempForm.action = action;
      tempForm.style.display = 'none';
      
      // Copy all form data
      for (const [key, value] of formData.entries()) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        tempForm.appendChild(input);
      }
      
      document.body.appendChild(tempForm);
      tempForm.submit();
      
      return false;
    });

    // Prevent form submission via Enter key without validation
    form.addEventListener("keydown", function (e) {
      if (e.key === "Enter" && e.target.tagName !== "TEXTAREA") {
        const submitButton = form.querySelector('button[type="submit"]');
        if (submitButton && !ValidationState.getValid()) {
          e.preventDefault();
          submitButton.click();
        }
      }
    });
  }

  // Export to global scope
  window.FormValidator = {
    init: initFormValidation,
    validate: validateForm,
    validateEmail: validateEmail,
    validatePhone: validatePhone,
    validateRequired: validateRequired,
  };

  // Make it harder to override
  Object.defineProperty(window, "FormValidator", {
    writable: false,
    configurable: false,
  });
})();
