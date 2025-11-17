<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($caHoc) ? 'Sửa ca học' : 'Thêm ca học' ?> - Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #333;
            margin-bottom: 30px;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        
        .form-group label.required::after {
            content: ' *';
            color: #dc3545;
        }
        
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            font-family: inherit;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0,123,255,0.1);
        }
        
        textarea.form-control {
            resize: vertical;
            min-height: 100px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            transition: all 0.3s;
        }
        
        .btn-primary {
            background: #007bff;
            color: white;
        }
        
        .btn-primary:hover {
            background: #0056b3;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
        
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <span><?= isset($caHoc) ? 'Sửa ca học' : 'Thêm ca học mới' ?></span>
                <a href="?act=admin-dashboard" class="btn" style="background: #6c757d; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px;">🏠 Trang chủ</a>
            </div>
        </h1>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" 
              action="?act=<?= isset($caHoc) ? 'admin-update-ca-hoc' : 'admin-save-ca-hoc' ?>">
            
            <?php if (isset($caHoc)): ?>
                <input type="hidden" name="id" value="<?= $caHoc['id'] ?>">
            <?php endif; ?>

            <div class="form-group">
                <label for="id_lop" class="required">Lớp học</label>
                <select name="id_lop" id="id_lop" class="form-control" required>
                    <option value="">-- Chọn lớp học --</option>
                    <?php foreach ($lopHocList as $lh): ?>
                        <option value="<?= $lh['id'] ?>" 
                                <?= (isset($caHoc) && $caHoc['id_lop'] == $lh['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lh['ten_lop']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="id_ca" class="required">Ca học</label>
                    <select name="id_ca" id="id_ca" class="form-control" required>
                        <option value="">-- Chọn ca học --</option>
                        <?php if (isset($caMacDinhList) && !empty($caMacDinhList)): ?>
                            <?php foreach ($caMacDinhList as $ca): ?>
                                <option value="<?= $ca['id'] ?>" 
                                        <?= (isset($caHoc) && $caHoc['id_ca'] == $ca['id']) ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($ca['ten_ca']) ?> 
                                    (<?= date('H:i', strtotime($ca['gio_bat_dau'])) ?> - <?= date('H:i', strtotime($ca['gio_ket_thuc'])) ?>)
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="thu_trong_tuan" class="required">Thứ trong tuần</label>
                    <select name="thu_trong_tuan" id="thu_trong_tuan" class="form-control" required>
                        <option value="">-- Chọn thứ --</option>
                        <option value="Thứ 2" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 2') ? 'selected' : '' ?>>Thứ 2</option>
                        <option value="Thứ 3" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 3') ? 'selected' : '' ?>>Thứ 3</option>
                        <option value="Thứ 4" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 4') ? 'selected' : '' ?>>Thứ 4</option>
                        <option value="Thứ 5" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 5') ? 'selected' : '' ?>>Thứ 5</option>
                        <option value="Thứ 6" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 6') ? 'selected' : '' ?>>Thứ 6</option>
                        <option value="Thứ 7" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Thứ 7') ? 'selected' : '' ?>>Thứ 7</option>
                        <option value="Chủ nhật" <?= (isset($caHoc) && $caHoc['thu_trong_tuan'] == 'Chủ nhật') ? 'selected' : '' ?>>Chủ nhật</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label for="id_giang_vien">Giảng viên</label>
                <select name="id_giang_vien" id="id_giang_vien" class="form-control">
                    <option value="">-- Chọn giảng viên (tùy chọn) --</option>
                    <?php if (isset($giangVienList) && !empty($giangVienList)): ?>
                        <?php foreach ($giangVienList as $gv): ?>
                            <option value="<?= $gv['id'] ?>" 
                                    <?= (isset($caHoc) && !empty($caHoc['id_giang_vien']) && (int)$caHoc['id_giang_vien'] == (int)$gv['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($gv['ho_ten']) ?> (<?= htmlspecialchars($gv['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="id_phong">Phòng học</label>
                <select name="id_phong" id="id_phong" class="form-control">
                    <option value="">Chọn phòng học (tùy chọn)</option>
                    <?php foreach ($phongHocList ?? [] as $ph): ?>
                        <option value="<?= $ph['id'] ?>" <?= (isset($caHoc['id_phong']) && $caHoc['id_phong'] == $ph['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($ph['ten_phong']) ?> (Sức chứa: <?= $ph['suc_chua'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="ghi_chu">Ghi chú</label>
                <textarea name="ghi_chu" 
                          id="ghi_chu" 
                          class="form-control" 
                          rows="3"
                          maxlength="255"
                          placeholder="Nhập ghi chú (tùy chọn)"><?= htmlspecialchars($caHoc['ghi_chu'] ?? '') ?></textarea>
            </div>

            <div class="btn-group">
                <button type="submit" class="btn btn-primary">
                    <?= isset($caHoc) ? 'Cập nhật' : 'Thêm mới' ?>
                </button>
                <a href="?act=admin-list-ca-hoc" class="btn btn-secondary">Hủy</a>
            </div>
        </form>
    </div>
</body>
</html>

