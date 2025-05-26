@extends('layouts.master')

@section('content')
    <style>
        .select-dropdown-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .select-dropdown-menu {
            display: none;
            position: absolute;
            width: 550px;
            max-height: 550px;
            /*overflow-y: auto;*/
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            padding: 10px;
            background: white;
            z-index: 1000;
        }

        .sortable-list {
            list-style: none;
            padding: 0;
            margin: 0;
            overflow-y: auto;
            max-height: 470px;
        }

        .sortable-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 15px;
            background: #f8f9fa;
            border-radius: 8px;
            border: 1px solid #ddd;
            margin-bottom: 8px;
            cursor: pointer;
            transition: 0.3s;
        }

        .sortable-list li:hover {
            background: #e9ecef;
        }

        .group-name {
            font-size: 16px;
            font-weight: 500;
            flex-grow: 1;
        }

        .action-icons {
            display: flex;
            gap: 8px;
        }

        .selected {
            background: #007bff !important;
            color: white;
        }

        .selected i {
            color: white;
        }

        .search-box {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .btn-dropdown-group {
            width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            border: 1px solid #cfcfcf;
            background: #fff;
        }

        .go-top-btn,
        .edit-btn,
        .delete-btn {
            /*color: #17a2b8;*/
            cursor: pointer;
        }

        .edit-btn:hover,
        .delete-btn:hover,
        .go-top-btn:hover {
            color: #000;
        }
    </style>
    <style>
        :root {
            --primary: #4361ee;
            --primary-dark: #3a56d4;
            --primary-light: #6f8aff;
            --secondary: #3f37c9;
            --success: #38b000;
            --success-light: #70e000;
            --danger: #ef233c;
            --danger-light: #ff4d6d;
            --warning: #fb8500;
            --info: #3a86ff;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --gray-light: #e9ecef;
            --gray-dark: #495057;
            --body-bg: #f9fafb;
            --border-radius: 0.5rem;
            --border-radius-lg: 0.75rem;
            --border-radius-sm: 0.25rem;
            --box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --box-shadow-lg: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --transition: all 0.3s ease;
            --font-family: 'Segoe UI', 'Roboto', sans-serif;
        }

        .content-page>.content {
            min-height: 90vh;
        }

        /* Card styles */
        .card {
            border: none;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
            margin-bottom: 1.5rem;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .card:hover {
            box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.08);
        }

        .card-header {
            background-color: white;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            padding: 1.25rem 1.5rem;
            border-top-left-radius: var(--border-radius-lg) !important;
            border-top-right-radius: var(--border-radius-lg) !important;
        }

        /* Button styling */
        .btn {
            border-radius: var(--border-radius);
            padding: 0.5rem 1.25rem;
            transition: var(--transition);
            font-weight: 500;
            letter-spacing: 0.01em;
            text-transform: none;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
            position: relative;
            overflow: hidden;
        }

        .btn::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 5px;
            height: 5px;
            background: rgba(255, 255, 255, 0.4);
            opacity: 0;
            border-radius: 100%;
            transform: scale(1, 1) translate(-50%);
            transform-origin: 50% 50%;
        }

        .btn:active::after {
            animation: ripple 0.6s ease-out;
        }

        @keyframes ripple {
            0% {
                transform: scale(0, 0);
                opacity: 0.5;
            }

            100% {
                transform: scale(20, 20);
                opacity: 0;
            }
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            border: none;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.3);
            border: none;
        }

        .btn-outline-primary {
            color: var(--primary);
            border-color: var(--primary);
            background: transparent;
        }

        .btn-outline-primary:hover {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(67, 97, 238, 0.2);
        }

        .btn-outline {
            background-color: white;
            border: 1px solid #e1e5eb;
            color: var(--gray);
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
        }

        .btn-outline:hover {
            border-color: var(--primary);
            color: var(--primary);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.06);
        }

        .btn-circle-cus2 {
            background: rgba(255, 255, 255, 0.9);
            color: #4361ee;
            border: none;
            border-radius: 50%;
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            transition: all 0.2sease;
            cursor: pointer;
        }

        /* Table improvements */
        .table {
            margin-bottom: 0;
        }

        .table th {
            font-weight: 600;
            color: var(--dark);
            border-top: none;
            background-color: #f8f9fa;
            padding: 1rem 0.75rem;
            position: sticky;
            top: 0;
            z-index: 10;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }

        .table td {
            vertical-align: middle;
            padding: 0.85rem 0.75rem;
            border-color: rgba(0, 0, 0, 0.05);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(67, 97, 238, 0.03);
            transition: background-color 0.2s ease;
        }

        .channel-row.selected {
            background-color: rgba(67, 97, 238, 0.08);
        }

        /* Badge styling */
        .badge {
            padding: 0.5em 0.85em;
            font-weight: 500;
            border-radius: 50rem;
            letter-spacing: 0.03em;
            font-size: 75%;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.06);
        }

        .badge-moonshots {
            background: linear-gradient(45deg, var(--success), var(--success-light));
            color: white;
        }

        .badge-boom {
            background: linear-gradient(45deg, var(--danger), var(--danger-light));
            color: white;
        }

        .badge-main {
            background: linear-gradient(45deg, var(--info), #5A9DF9);
            color: white;
        }

        /* Floating Action Panel styles */
        .action-panel {
            position: fixed;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            width: 440px;
            background-color: white;
            border-radius: var(--border-radius-lg);
            box-shadow: 0 0.75rem 2rem rgba(0, 0, 0, 0.15);
            z-index: 1000;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            opacity: 0;
            pointer-events: none;
            overflow: hidden;
            /* border: 1px solid rgba(0, 0, 0, 0.08); */
        }

        .action-panel.show {
            opacity: 1;
            pointer-events: all;
        }

        .action-panel-header {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white;
        }

        .action-panel-body {
            padding: 1.25rem 1.5rem;
            overflow-y: auto;
        }

        .action-panel-footer {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
            background-color: #fff;
        }

        /* Execute button styling */
        .execute-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.85rem;
            font-weight: 600;
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            border: none;
            transition: all 0.3s ease;
        }

        .execute-btn:hover:not(:disabled) {
            background: linear-gradient(45deg, var(--primary), var(--primary-light));
            transform: translateY(-2px);
            box-shadow: 0 8px 15px rgba(67, 97, 238, 0.3);
            border: none;
        }

        .execute-btn:disabled {
            /*background: linear-gradient(45deg, #b8c2cc, #d1d9e6);*/
            /*background: linear-gradient(45deg, #007bff, #007bff);*/
            cursor: not-allowed;
            color: rgba(255, 255, 255, 0.8);
        }

        /* Search input styling */
        .action-search {
            margin-bottom: 1.25rem;
        }

        .action-search input {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            border: 1px solid rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }

        .action-search input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
        }

        /* Action groups styling */
        .action-group {
            margin-bottom: 1.5rem;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            background-color: rgba(248, 249, 250, 0.5);
            border: 1px solid rgba(0, 0, 0, 0.03);
            transition: var(--transition);
        }

        .action-group:hover {
            background-color: rgba(248, 249, 250, 0.8);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }

        .action-group-title {
            font-weight: 600;
            margin-bottom: 0.85rem;
            color: var(--dark);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .action-btn {
            padding: 0.5rem 0.85rem;
            font-size: 0.8rem;
            border-radius: var(--border-radius-sm);
            transition: all 0.2s ease;
            white-space: nowrap;
            text-overflow: ellipsis;
            overflow: hidden;
            border: 1px solid rgba(0, 0, 0, 0.1);
            background-color: white;
            color: var(--gray-dark);
        }

        .action-btn:hover {
            background-color: rgba(67, 97, 238, 0.05);
            border-color: rgba(67, 97, 238, 0.2);
            color: var(--primary);
            /*transform: translateY(-1px);*/
        }

        .action-btn.active {
            background-color: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .form-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .form-container.show {
            max-height: 500px;
        }

        .action-form.show {
            display: block;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Filter panel styling */
        .filter-panel {
            background-color: white;
            border-radius: var(--border-radius-lg);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            display: none;
            box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.05);
            border: 1px solid rgba(0, 0, 0, 0.05);
            animation: slideDown 0.3s ease-out;
        }

        .filter-panel.show {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Pagination styling */
        .pagination {
            margin-bottom: 0;
        }

        .page-item.active .page-link {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary));
            border-color: var(--primary);
            box-shadow: 0 2px 5px rgba(67, 97, 238, 0.3);
        }

        .page-link {
            border: none;
            padding: 0.5rem 0.85rem;
            color: var(--primary);
            border-radius: var(--border-radius-sm);
            margin: 0 0.15rem;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background-color: rgba(67, 97, 238, 0.1);
            color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 2px 5px rgba(67, 97, 238, 0.1);
        }

        .page-item.disabled .page-link {
            background-color: transparent;
            color: var(--gray);
            opacity: 0.6;
        }

        /* Custom checkbox styling */
        .custom-control-label::before {
            border-radius: var(--border-radius-sm);
            border: 2px solid rgba(0, 0, 0, 0.15);
            background-color: white;
            transition: all 0.2s ease;
        }

        .custom-control-input:checked~.custom-control-label::before {
            background-color: var(--primary);
            border-color: var(--primary);
            box-shadow: 0 0 0 2px rgba(67, 97, 238, 0.2);
        }

        .custom-control-input:focus~.custom-control-label::before {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.25);
            border-color: var(--primary);
        }

        .action-item {
            display: flex;
            flex-direction: column;
            width: 100%;
            margin-bottom: 0.5rem;
        }

        .action-btn-group {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            flex-direction: column;
        }

        .action-btn-group>button {
            align-self: flex-start;
        }

        .action-form {
            display: none;
            margin-top: 0.5rem;
            margin-bottom: 0.75rem;
            padding: 0.75rem;
            border-radius: var(--border-radius);
            background-color: var(--gray-light);
            border: 1px solid rgba(0, 0, 0, 0.05);
            font-size: 0.9rem;
            width: 100%;
            animation: fadeIn 0.2s ease-out;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .action-form h6 {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--primary);
        }

        .action-form .form-control {
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
        }

        .action-form label {
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .form-section {
            margin-bottom: 12px;
            padding: 10px;
            background-color: rgba(255, 255, 255, 0.6);
            border-radius: 6px;
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .form-section-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 12px;
            color: var(--primary);
            border-bottom: 1px solid rgba(67, 97, 238, 0.2);
            padding-bottom: 6px;
        }

        .form-section-subtitle {
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            color: var(--dark);
        }

        .action-form .form-control-sm {
            height: calc(1.5em + 0.5rem + 2px);
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            border-radius: 0.2rem;
        }

        .action-form .form-control-sm:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 0 0.2rem rgba(67, 97, 238, 0.15);
        }

        /* Làm cho select multiple đẹp hơn */
        .action-form select[multiple] {
            height: 120px;
        }

        #executeReloadBtn:hover:not(:disabled) i {
            animation: spin 1.5s linear infinite;
        }

        .action-panel.closing {
            opacity: 0;
            transform: translateY(-50%) translateX(50px);
            transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 992px) {
            .action-panel {
                width: 380px;
                right: 1rem;
                max-height: 85vh;
            }

            .action-panel-body {
                padding: 1rem;
            }

            .action-btn {
                font-size: 0.75rem;
                padding: 0.4rem 0.7rem;
            }

            .action-form {
                padding: 0.6rem;
            }
        }

        /* Các điều chỉnh cho màn hình mobile */
        @media (max-width: 768px) {
            .action-panel {
                width: calc(100% - 2rem);
                /* Chiếm hầu hết chiều rộng màn hình */
                right: 1rem;
                left: 1rem;
                max-height: 80vh;
                top: auto;
                /* Không dùng căn giữa theo chiều dọc */
                bottom: 1rem;
                /* Gắn panel ở dưới màn hình */
                transform: none;
                /* Bỏ transform translateY */
                border-radius: 0.75rem;
            }

            .action-panel.closing {
                transform: translateY(50px);
                /* Hiệu ứng đóng trượt xuống */
                opacity: 0;
            }

            .action-panel-body {
                padding: 0.75rem;
                max-height: calc(80vh - 120px);
                /* Đảm bảo có không gian cho header và footer */
            }

            /* Cải thiện kích thước nút và khoảng cách */
            .action-btn-group {
                gap: 0.35rem;
            }

            .action-btn {
                font-size: 0.7rem;
                padding: 0.35rem 0.6rem;
            }

            /* Điều chỉnh form để hiển thị tốt hơn trên mobile */
            .action-form {
                padding: 0.5rem;
                margin-top: 0.3rem;
                margin-bottom: 0.5rem;
            }

            .action-form label {
                font-size: 0.7rem;
                margin-bottom: 0.15rem;
            }

            .action-form .form-control {
                font-size: 0.8rem;
                padding: 0.3rem 0.5rem;
            }

            /* Điều chỉnh footer để các nút Execute rõ ràng hơn */
            .action-panel-footer {
                padding: 0.75rem;
            }

            .execute-btn {
                padding: 0.6rem 0.5rem;
                font-size: 0.85rem;
            }

            /* Điều chỉnh header */
            .action-panel-header {
                padding: 0.75rem 1rem;
            }

            .action-panel-header h5 {
                font-size: 1rem;
            }
        }

        /* Điều chỉnh cho màn hình rất nhỏ */
        @media (max-width: 480px) {
            .action-panel {
                width: calc(100% - 1rem);
                right: 0.5rem;
                left: 0.5rem;
                bottom: 0.5rem;
            }

            /* Thay đổi layout cho nút Execute trên màn hình rất nhỏ */
            .action-panel-footer .row {
                margin: 0 -5px;
            }

            .action-panel-footer .col-6 {
                padding: 0 5px;
            }

            .execute-btn {
                padding: 0.5rem 0.25rem;
                font-size: 0.75rem;
            }

            /* Tối ưu hóa không gian: ẩn một số phần không cần thiết */
            .action-group-title {
                font-size: 0.8rem;
                margin-bottom: 0.5rem;
            }

            /* Làm cho form gọn gàng hơn */
            .form-row {
                margin-right: -5px;
                margin-left: -5px;
            }

            .form-row>.col,
            .form-row>[class*="col-"] {
                padding-right: 5px;
                padding-left: 5px;
            }

            .action-form .form-group {
                margin-bottom: 0.5rem;
            }

            /* Tối ưu cho các checkbox trên mobile */
            .custom-control-label {
                font-size: 0.8rem;
            }

            .btn-100 {
                margin-top: 0.5rem;
                width: 100%;
            }

            .checkbox label::before {
                margin-left: 0px;
            }

            .select-dropdown-menu {
                width: 300px;
            }
        }

        /* Fix cho màn hình nằm ngang trên mobile */
        @media (max-height: 600px) {
            .action-panel {
                max-height: 90vh;
                top: 5vh;
                transform: none;
            }

            .action-panel-body {
                max-height: calc(90vh - 110px);
            }
        }
    </style>
    <style>
        /* Renamed avatar styling with edit button */
        .channel-avatar-v1 {
            position: relative;
            margin: 0 20px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .channel-avatar-v1 img {
            object-fit: cover;
            border-width: 4px;
            border-style: solid;
        }

        /* Simple color coding for avatars */
        .channel-avatar-v1.branded img {
            border-color: #4361ee;
            /* Blue for branded channels */
        }

        .channel-avatar-v1.not-branded img {
            border-color: #fb8500;
            /* Yellow/orange for non-branded */
        }

        .channel-avatar-v1.brand-error img {
            border-color: #ef233c;
            /* Red for brand error */
        }

        /* Edit button overlay - positioned center like the example image */
        /*        .avatar-edit-btn {
                position: absolute;
                bottom: 50%;
                right: 50%;
                transform: translate(50%, 50%);
                background: rgba(255, 255, 255, 0.9);
                color: #212529;
                border: none;
                border-radius: 50%;
                width: 36px;
                height: 36px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 16px;
                box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
                opacity: 0;
                transition: opacity 0.2s ease;
            }*/

        .channel-avatar-v1:hover .avatar-edit-btn {
            opacity: 1;
        }

        .avatar-sync-btn {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(255, 255, 255, 0.9);
            color: #4361ee;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            transition: all 0.3s ease;
            opacity: 0;
            cursor: pointer;
            z-index: 2;
        }

        .avatar-edit-btn {
            position: absolute;
            bottom: 0px;
            right: 0px;
            background: rgba(255, 255, 255, 0.9);
            color: #212529;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 1;
            cursor: pointer;
        }

        /*        .avatar-sync-btn:hover {
                transform: rotate(180deg);
                background: white;
                color: #3a56d4;
                box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
            }*/
        .avatar-sync-btn:hover {
            background: white;
            transform: translate(-50%, -50%) rotate(180deg);
        }

        .channel-avatar-v1:hover .avatar-sync-btn {
            opacity: 1;
        }


        /* Channel info organization */
        .channel-info {
            flex: 1;
        }

        .detail-row {
            margin-bottom: 6px;
        }

        .detail-item {
            display: flex;
            align-items: center;
        }

        /* Simplified badges for Tags column */
        .badge {
            padding: 0.4em 0.7em;
            border-radius: 4px;
            font-weight: 500;
            font-size: 0.8rem;
            margin-right: 4px;
            margin-bottom: 4px;
        }

        /* Simplified action buttons */
        .action-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            align-items: center;
        }

        .action-buttons .btn-sm {
            padding: 0.25rem 0.6rem;
            font-size: 0.8rem;
            border-radius: 4px;
        }

        /* Updated table header for Tags column instead of Information */
        .th-tags {
            width: 140px;
        }

        .btn-action {
            background-color: #f8f9fa;
            border: 1px solid #e9ecef;
            color: #6c757d;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.2s ease;
        }

        .btn-action:hover {
            background-color: #e9ecef;
            color: #495057;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        /* Copyable text styling */
        .copyable-text,
        .copyable-channel {
            cursor: pointer;
            position: relative;
            border-bottom: 1px dotted #dee2e6;
            transition: all 0.2s ease;
        }

        .copyable-text:hover,
        .copyable-channel:hover {
            color: #4361ee;
            border-bottom-color: #4361ee;
        }

        .copyable-text:after,
        .copyable-channel:after {
            content: "Copied!";
            position: absolute;
            left: 50%;
            top: -20px;
            transform: translateX(-50%);
            background-color: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 0.7rem;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .copyable-text.copied:after,
        .copyable-channel.copied:after {
            opacity: 1;
            animation: fadeOutAfter 2s forwards;
        }

        @keyframes fadeOutAfter {
            0% {
                opacity: 1;
            }

            70% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        /* Vertical tags */
        .tags-container {
            display: flex;
            flex-direction: column;
        }

        .tags-container .badge {
            width: fit-content;
            position: relative;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        /* Growth arrow and percentage */
        .growth-arrow {
            font-size: 0.7rem;
            vertical-align: middle;
        }

        .growth-percentage {
            font-size: 0.95rem;
            font-weight: 500;
        }

        /* Hub status */
        .hub-status {
            display: inline-block;
            padding: 0.2rem 0.6rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-size: 0.85rem;
            background-color: #f8f9fa;
        }

        /* Brand status styling for all conditions */
        /* Already branded (success) */
        .channel-avatar-v1.branded img {
            border-color: #45AF49;
            /* Blue for branded channels */
        }

        /* Not branded yet */
        .channel-avatar-v1.not-branded img {
            border-color: #fb8500;
            /* Yellow/orange for not-branded */
        }

        /* Brand error */
        .channel-avatar-v1.brand-error img {
            border-color: #ef233c;
            /* Red for brand error */
        }

        /* Enhanced avatar fallback handling */
        .channel-avatar-v1 img {
            object-fit: cover;
            border-width: 5px;
            border-style: solid;
            width: 100px;
            height: 100px;
        }

        .channel-avatar-v1 img.error {
            display: none;
        }

        .channel-avatar-v1 img.error+.avatar-fallback {
            display: flex;
        }

        .avatar-fallback {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            border-width: 4px;
            border-style: solid;
            border-color: inherit;
        }

        /* Enhancement for table header text */
        .table th {
            font-weight: 700;
            color: #343a40;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.05em;
        }


        /* Additional channel name styling */
        .channel-name {
            cursor: pointer;
        }

        .avatar-fallback {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background-color: #e9ecef;
            color: #6c757d;
            display: none;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            border-width: 4px;
            border-style: solid;
            border-color: inherit;
            position: relative;
        }

        .filter-group {
            background-color: #f9fafc;
            border: 1px solid #edf0f7;
            border-radius: 8px;
            margin-bottom: 20px;
            /*overflow: hidden;*/
        }

        .filter-group-header {
            padding: 12px 15px;
            background-color: #f0f2ff;
            border-bottom: 1px solid #edf0f7;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .filter-group-title {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 500;
            color: #3854e0;
        }

        .filter-group-title i {
            margin-right: 8px;
            font-size: 0.95rem;
        }

        .filter-group-content {
            padding: 15px;
        }

        .filter-groups-row {
            display: flex;
            flex-wrap: wrap;
            margin-right: -10px;
            margin-left: -10px;
        }

        .select-wrapper {
            position: relative;
        }

        .header-search-container {
            /*width: 40%;*/
            min-width: 300px;
            position: relative;
        }

        .header-search-container .input-group {
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .search-input {
            height: calc(1.5em + 0.75rem + 4px);
            border-color: #e9ecef;
            font-size: 0.95rem;
            padding-right: 40px;
            /* Tạo khoảng trống cho biểu tượng */
            border-radius: var(--border-radius) !important;
        }

        .search-input:focus {
            box-shadow: none;
            border-color: var(--primary-light);
        }

        .search-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;
            pointer-events: none;
            /* Cho phép click xuyên qua biểu tượng tới input */
        }

        .tags-container .badge-primary {
            background-color: #4361ee;
            border: none;
        }

        .tags-container .badge {
            padding-right: 20px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .tags-container .badge-primary:hover {
            background-color: #3a56d4;
        }

        .tag-delete {
            position: absolute;
            top: 50%;
            right: 5px;
            transform: translateY(-50%);
            font-size: 0.7rem;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.2s ease;
            z-index: 2;
        }

        .tags-container .badge:hover .tag-delete {
            opacity: 1;
        }

        .add-tag-btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            width: 100%;
            border-style: dashed;
            border-width: 1px;
            border-color: #adb5bd;
            color: #6c757d;
        }

        .tag-delete:hover {
            opacity: 1;
        }

        .add-tag-btn {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            width: 100%;
            border-style: dashed;
            border-width: 1px;
            border-color: #adb5bd;
            color: #6c757d;
        }

        .add-tag-btn:hover {
            background-color: #f8f9fa;
            color: #4361ee;
            border-color: #4361ee;
        }

        .filter-badge {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            font-size: 10px;
            position: absolute;
            top: -8px;
            right: -8px;
            font-weight: bold;
        }

        .gmail-count {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 16px;
            height: 16px;
            background-color: red;
            color: white;
            border-radius: 50%;
            font-size: 11px;
            font-weight: normal;
            margin-left: 5px;
        }


        .tag-suggestions-list {
            max-height: 200px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
            display: none;
        }

        .tag-suggestion-item {
            padding: 8px 12px;
            cursor: pointer;
        }

        .tag-suggestion-item:hover {
            background-color: #f8f9fa;
        }


        .approved {
            background-color: #28a745;
        }

        .status-icon {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 10px;
            display: inline-block;
        }

        .channel-header {
            transition: all 0.3s ease;
        }

        .stat-value {
            font-weight: bold;
            color: #2d3436;
        }

        .stat-label {
            font-size: 0.8rem;
            color: #636e72;
            text-transform: uppercase;
        }

        .stat-item {
            min-width: 80px;
        }

        table td:nth-child(7) {
            width: 160px;
            min-width: 160px;
            max-width: 160px;
            position: relative;
        }

        .error-container {
            position: relative;
            width: 150px;
            cursor: pointer;
            padding: 2px 0;
        }

        .error-display {
            transition: all 0.2s ease;
            border-bottom: 1px dashed transparent;
        }

        .error-container:hover .error-display {
            border-bottom: 1px dashed #dc3545;
        }

        .error-text {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
            font-size: 0.875rem;
        }

        .error-container:hover .error-text {
            font-weight: 500;
        }
        
        .error-actions {
            display: flex;
            opacity: 0;
            transition: all 0.2s ease;
        }
        .error-container:hover .error-actions {
            opacity: 1;
        }        

        .error-details {
            position: absolute;
            top: 100%;
            left: 0;
            background: #333;
            color: #fff;
            border-radius: 3px;
            padding: 8px 10px;
            z-index: 1000;
            width: 280px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
            font-size: 0.85em;
            margin-top: 5px;
            white-space: pre-wrap;
            word-break: break-word;
        }

        .error-details:after {
            content: "";
            position: absolute;
            bottom: 100%;
            left: 20px;
            border-width: 5px;
            border-style: solid;
            border-color: transparent transparent #333 transparent;
        }

        .btn-remove-error,
        .btn-resolve-error,
        .btn-check-error{
            opacity: 1;
            background: none;
            border: none;
            color: #dc3545;
            width: 28px;
            height: 28px;
            font-size: 16px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

/*        .error-container:hover .btn-remove-error {
            opacity: 1;
        }*/

/*        .btn-remove-error:hover {
            background-color: rgba(220, 53, 69, 0.1);
            transform: scale(1.1);
        }

        .btn-remove-error:active {
            background-color: rgba(220, 53, 69, 0.2);
            transform: scale(0.95);
        }*/

.btn-remove-error {
    color: #dc3545;
    font-size: 14px;
}

.btn-resolve-error {
    color: #28a745;
    font-size: 14px;
}

.btn-remove-error:hover {
    background-color: rgba(220, 53, 69, 0.1);
}

.btn-resolve-error:hover {
    background-color: rgba(40, 167, 69, 0.1);
}
.btn-check-error {
    color: #007bff;
}

.btn-check-error:hover {
    background-color: rgba(0, 123, 255, 0.1);
    transform: scale(1.1);
}




        #modal_multi_chart_realtime .channel-header {
            position: relative;
            padding: 15px 0;
            /* margin-bottom: 20px; */
            transition: all 0.3s ease;
        }

        #modal_multi_chart_realtime .channel-info {
            display: flex;
            align-items: flex-start;
            flex-direction: row;
        }

        #modal_multi_chart_realtime .channel-avatar {
            position: relative;
            width: 60px;
            height: 60px;
            margin-right: 15px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        #modal_multi_chart_realtime .channel-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }


        #modal_multi_chart_realtime .channel-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
        }

        #modal_multi_chart_realtime .channel-stats {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        #modal_multi_chart_realtime .stat-item {
            display: flex;
            align-items: center;
        }

        #modal_multi_chart_realtime .stat-item i {
            color: #6c5ce7;
            margin-right: 5px;
            font-size: 14px;
        }

        #modal_multi_chart_realtime .stat-value {
            font-weight: 500;
            color: #444;
        }

        #modal_multi_chart_realtime .stat-label {
            color: #666;
            margin-left: 5px;
            font-size: 13px;
        }

        #modal_multi_chart_realtime .channel-actions {
            position: absolute;
            top: 15px;
            right: 0;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        #modal_multi_chart_realtime .channel-header:hover .channel-actions {
            opacity: 1;
        }

        #modal_multi_chart_realtime .remove-channel-btn {
            border: none;
            background-color: #ff6b6b;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        #modal_multi_chart_realtime .remove-channel-btn:hover {
            background-color: #ff5252;
            transform: scale(1.1);
        }

        #modal_multi_chart_realtime .remove-channel-btn i {
            font-size: 14px;
        }



        #modal_multi_chart_realtime .channel-avatar {
            position: relative;
            width: 60px;
            height: 60px;
            margin-right: 15px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
        }

        #modal_multi_chart_realtime .channel-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        #modal_multi_chart_realtime .channel-details {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        #modal_multi_chart_realtime .channel-name {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        #modal_multi_chart_realtime .channel-stats {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }




        #modal_multi_chart_realtime .error-container {
            width: 100%;
            margin-bottom: 20px;
        }

        #modal_multi_chart_realtime .error-container .fa-exclamation-triangle {
            color: #ffc107;
        }

        #modal_multi_chart_realtime .error-container .alert {
            border-left: 4px solid #ffc107;
            background-color: #fff8e1;
            font-size: 14px;
            margin-bottom: 0;
            padding: 10px 15px;
        }

        #modal_multi_chart_realtime .error-container .btn-sm {
            font-size: 12px;
            padding: 4px 8px;
        }

        #modal_multi_chart_realtime .error-container .btn-outline-primary {
            transition: all 0.3s ease;
            border-color: #007bff;
        }

        #modal_multi_chart_realtime .error-container .btn-outline-primary:hover {
            background-color: #007bff;
            color: white;
        }

        #modal_multi_chart_realtime .error-container .btn-outline-primary:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        #modal_multi_chart_realtime .remove-chart-btn {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

#modal_multi_chart_realtime .remove-chart-btn:hover {
    background-color: rgba(220, 53, 69, 0.2);
}

/* chart 2 kenh 1 dong*/
#modal_multi_chart_realtime .channel-row {
    display: flex;
    margin: 0 -10px 20px -10px;
    position: relative;
}

#modal_multi_chart_realtime .channel-col {
    flex: 0 0 50%;
    padding: 0 10px;
    box-sizing: border-box;
}

#modal_multi_chart_realtime .channel-container {
    border: 1px solid #eee;
    border-radius: 8px;
    height: 100%;
    position: relative;
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
}

#modal_multi_chart_realtime .channel-header {
    padding: 15px;
    position: relative;
}

#modal_multi_chart_realtime .channel-actions {
    position: absolute;
    right: 10px;
    top: 10px;
    display: flex;
    gap: 5px;
}

#modal_multi_chart_realtime .move-buttons {
    position: absolute;
    right: 10px;
    top: 40px; /* Đặt dưới nút xóa */
    display: flex;
    flex-direction: column;
    gap: 3px;
}

#modal_multi_chart_realtime .action-btn {
    border: none;
    background: rgba(0,0,0,0.05);
    color: #555;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

#modal_multi_chart_realtime .action-btn:hover {
    background: rgba(0,0,0,0.1);
}

#modal_multi_chart_realtime .remove-btn {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

#modal_multi_chart_realtime .remove-btn:hover {
    background-color: rgba(220, 53, 69, 0.2);
}

#modal_multi_chart_realtime .move-up-btn,
#modal_multi_chart_realtime .move-down-btn {
    background-color: rgba(0, 123, 255, 0.1);
    color: #007bff;
}

#modal_multi_chart_realtime .move-up-btn:hover,
#modal_multi_chart_realtime .move-down-btn:hover {
    background-color: rgba(0, 123, 255, 0.2);
}

/* CSS cho nút sắp xếp */
#modal_multi_chart_realtime .sort-buttons {
    margin: 0 0 15px 0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
}

#modal_multi_chart_realtime .sort-btn {
    padding: 5px 10px;
    background-color: #f8f9fa;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 12px;
    color: #555;
    cursor: pointer;
    transition: all 0.2s ease;
}

#modal_multi_chart_realtime .sort-btn:hover,
#modal_multi_chart_realtime .sort-btn.active {
    background-color: #007bff;
    border-color: #007bff;
    color: white;
}

/* CSS cho hiển thị lỗi */
#modal_multi_chart_realtime .horizontal-error {
    display: flex;
    align-items: center;
    padding: 10px 15px;
    background-color: #fff;
    border: 1px solid #f5c6cb;
    border-radius: 4px;
    margin-bottom: 20px;
}

#modal_multi_chart_realtime .error-icon {
    font-size: 20px;
    margin-right: 15px;
    color: #dc3545;
}

#modal_multi_chart_realtime .error-info {
    flex-grow: 1;
    font-size: 14px;
    color: #555;
}

#modal_multi_chart_realtime .btn-outline-danger {
    border-color: #dc3545;
    color: #dc3545;
    padding: 3px 10px;
    margin-left: 15px;
    white-space: nowrap;
}

#modal_multi_chart_realtime .btn-outline-danger:hover {
    background-color: #dc3545;
    color: white;
}
/* #modal_multi_chart_realtime .channel-header {
    padding: 15px;
    position: relative;
} */

#modal_multi_chart_realtime .channel-actions {
    position: absolute;
    right: 10px;
    top: 10px;
    display: flex;
    gap: 5px;
    opacity: 0;
    transition: opacity 0.2s ease;
}

#modal_multi_chart_realtime .channel-header:hover .channel-actions {
    opacity: 1;
}

#modal_multi_chart_realtime .action-btn {
    border: none;
    background: rgba(0,0,0,0.05);
    color: #555;
    width: 24px;
    height: 24px;
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
}

#modal_multi_chart_realtime .action-btn:hover {
    background: rgba(0,0,0,0.1);
}

#modal_multi_chart_realtime .remove-btn {
    background-color: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

#modal_multi_chart_realtime .remove-btn:hover {
    background-color: rgba(220, 53, 69, 0.2);
}

#modal_multi_chart_realtime .move-up-btn,
#modal_multi_chart_realtime .move-down-btn {
    background-color: rgba(0, 123, 255, 0.1);
    color: #007bff;
}

#modal_multi_chart_realtime .move-up-btn:hover,
#modal_multi_chart_realtime .move-down-btn:hover {
    background-color: rgba(0, 123, 255, 0.2);
}

/* CSS cho avatar */
#modal_multi_chart_realtime .channel-avatar {
    margin-right: 15px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

#modal_multi_chart_realtime .channel-avatar img {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    object-fit: cover;
}

/* Responsive adjustments */
@media (max-width: 767px) {
    #modal_multi_chart_realtime .channel-stats {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }

    #modal_multi_chart_realtime .channel-avatar {
        width: 50px;
        height: 50px;
    }

    #modal_multi_chart_realtime .channel-name {
        font-size: 16px;
    }
}

/*    .btn-finish-recovery {
        width: 30px; 
        height: 30px; 
        border-radius: 50%; 
        border: none; 
        background: linear-gradient(45deg, #45AF49, #7ce17c); 
        color: white; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        box-shadow: 0 3px 8px rgba(69, 175, 73, 0.5); 
        transition: all 0.3s ease; 
        vertical-align: middle;
        outline: none;
        position: relative;
        z-index: 10;
        margin-left: 8px;
    }
    
    .btn-finish-recovery:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(69, 175, 73, 0.7);
    }*/

    .btn-finish-recovery {
        width: 30px; 
        height: 30px; 
        border-radius: 50%; 
        border: none; 
        background: linear-gradient(45deg, #45AF49, #7ce17c); 
        color: white; 
        display: inline-flex; 
        align-items: center; 
        justify-content: center; 
        box-shadow: 0 3px 8px rgba(69, 175, 73, 0.5); 
        transition: all 0.3s ease; 
        vertical-align: middle;
        outline: none;
        position: relative;
        z-index: 10;
        margin-left: 8px;
    }
    
    .btn-finish-recovery:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(69, 175, 73, 0.7);
    }
    
    .recovery-email-container {
        display: inline-flex;
        align-items: center;
        background-color: #f8f9fa;
        border-radius: 4px;
        padding: 2px 8px;
        margin-left: 12px;
        border: 1px solid #e9ecef;
    }
    
    .btn-gologin.locked {
        opacity: 0.7;
        cursor: not-allowed;
        position: relative;
    }

    .btn-gologin.locked::after {
        content: "";
        position: absolute;
        top: 0;
        left: 0;
        height: 100%;
        background-color: rgba(255, 255, 255, 0.2);
        animation: btn-loading-animation 10s linear forwards;
    }

    .btn-gologin.highlighted {
        box-shadow: 0 0 0 2px #ee43d9, 0 0 10px #ee434b;
        transform: translateY(-1px);
        background-color: #e9ecef;
        transition: all 0.2s ease;
    }

    @keyframes btn-loading-animation {
        from { width: 0; }
        to { width: 100%; }
    }    
    </style>

    <div id="filterPanel" class="filter-panel">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Advanced Filters</h5>
            <button id="closeFilterBtn" class="btn-circle-cus2">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                    class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                    <path
                        d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z">
                    </path>
                </svg>
            </button>
        </div>


        <form id="form-search" action="/channelmanagement/v2">
            <input type="hidden" name="limit" id="limit" value="{{ $limit }}">
            <input id="is_change_info_error" type="hidden" name="is_change_info_error" value="{{$request->is_change_info_error}}"/>
            <input id="is_upload_error" type="hidden" name="is_upload_error" value="{{$request->is_upload_error}}"/>
            <div class="filter-groups-row">
                <!-- Left Column -->
                <div class="col-md-6 filter-group-wrapper">
                    <!-- Group 1: Basic Information -->
                    <div class="filter-group">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#basicInfoGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-info-circle"></i> Basic Information
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="basicInfoGroup">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">ID/Name/Email</label>
                                        <div class="col-12">
                                            <input id="search_channel" class="form-control" type="text" name="c1"
                                                value="{{ $request->c1 }}" placeholder="Search ID, name or email...">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Status</label>
                                        <div class="col-12">
                                            <select id="search_status" class="form-control" name="c2">
                                                {!! $status !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                @if ($is_admin_music)
                                    <div class="col-md-6">
                                        <div class="form-group row">
                                            <label class="col-6 col-form-label">User</label>
                                            <div class="col-12">
                                                <select id="user_search" class="form-control search_select" name="c5"
                                                    data-show-subtext="true" data-live-search="true">
                                                    {!! $listusercode !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-6">
                                    <label class="col-12 col-form-label">Group <span class="btn_add_group_channel"><i
                                                class="fa fa-plus-circle color-red"
                                                style="font-size: 20px;"></i></span></label>
                                    <div id="group_channel_1" class="select-dropdown-container"></div>
                                    <!--                                    <div class="select-dropdown-container">
                                                            <button class="btn dropdown-toggle btn-dropdown-group" type="button" id="dropdownMenuButton">
                                                                Select Group
                                                            </button>

                                                            <div class="select-dropdown-menu">
                                                                <input type="text" id="searchBox" class="search-box" placeholder="Search...">
                                                                <ul id="sortableList" class="sortable-list div_scroll_50"></ul>
                                                            </div>
                                                        </div>-->
                                    <input type="hidden" id="group_channel_search" name="c3"
                                        value="{{ $request->c3 }}" />
                                </div>
                                <div class="col-md-12 mt-3">
                                    <label for="gmail_log">Log</label>
                                    <input id="gmail_log" class="form-control" type="text" name="gmail_log" value="{{$request->gmail_log}}"
                                        placeholder="Enter log information...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Group 2: Content Classification -->
                    <div class="filter-group mt-4">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#contentGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-tags"></i> Content Classification
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="contentGroup">
                            <div class="row">


                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-6 col-form-label">Genre</label>
                                        <div class="col-12">
                                            <select class="form-control" name="channel_genre">
                                                {!! $channelGenre !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Channel Type</label>
                                        <div class="col-12">
                                            <select id="other_filter" class="form-control" name="other_filter">
                                                <option value="-1">Select</option>
                                                <option value="1">Moonshots</option>
                                                <option value="2">Bas</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Studio</label>
                                        <div class="col-12">
                                            <select id="studio" class="form-control" name="studio">
                                                {!! $studio !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Tags</label>
                                        <div class="col-12">
                                            <select id="tags" name="tags[]" class="select2_multiple form-control "
                                                multiple style="height: 140px">
                                                {!! $channelTags !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div>

                <!-- Right Column -->
                <div class="col-md-6 filter-group-wrapper">
                    <!-- Group 4: Management Methods -->
                    <div class="filter-group">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#methodsGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-sliders-h"></i> Management Methods
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="methodsGroup">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Manage Type</label>
                                        <div class="col-12">
                                            <select class="form-control" name="c6">
                                                {!! $channelManageType !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Upload Type</label>
                                        <div class="col-12">
                                            <select class="form-control" name="c7">
                                                {!! $channelUploadType !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Wakeup Type</label>
                                        <div class="col-12">
                                            <select class="form-control" name="c8">
                                                {!! $channelWakeupType !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Upload By</label>
                                        <div class="col-12">
                                            <select id="version" class="form-control" name="version">
                                                <option value="-1">Select</option>
                                                <option value="2">Api Upload</option>
                                                <option value="1">Moonshots or Bas Upload</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Group 5: System Status -->
                    <div class="filter-group mt-4">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#statusGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-server"></i> System Status
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="statusGroup">
                            <div class="row">

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Hub</label>
                                        <div class="col-12">
                                            <select id="statusHub" class="form-control" name="statusHub">
                                                {!! $statusHubs !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Storm</label>
                                        <div class="col-12">
                                            <select id="level" class="form-control" name="level">
                                                {!! $level !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Is Sync</label>
                                        <div class="col-12">
                                            <select id="level" class="form-control" name="is_sync">
                                                {!! $isSync !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Comment</label>
                                        <div class="col-12">
                                            <select id="status_cmt" class="form-control" name="status_cmt">
                                                {!! $statusCmt !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Tracking</label>
                                        <div class="col-12">
                                            <select id="sub_tracking" class="form-control" name="sub_tracking">
                                                {!! $subTracking !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                @if ($is_admin_music)
                                    <div class="col-md-3">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">Not update MS</label>
                                            <div class="col-12">
                                                <div class="checkbox checkbox-primary">
                                                    <input id="outofdate_moonshot_stat" class="" type="checkbox"
                                                        name="outofdate_moonshot_stat" value="1"
                                                        {{ $request->moonshot_stat }}>
                                                    <label class=""
                                                        for="outofdate_moonshot_stat">{{ $stats }} channel</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Group 3: Technical Management -->
                    <div class="filter-group mt-4">
                        <div class="filter-group-header" data-toggle="collapse" data-target="#technicalGroup">
                            <h6 class="filter-group-title">
                                <i class="fas fa-cogs"></i> Technical Management
                            </h6>
                        </div>
                        <div class="filter-group-content collapse show" id="technicalGroup">
                            <div class="row">

                                @if ($is_admin_music)
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-6 col-form-label">API</label>
                                            <div class="col-12">
                                                <select class="form-control" name="c4">
                                                    {!! $statusApi !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group row">
                                            <label class="col-12 col-form-label">AutoLogin</label>
                                            <div class="col-12">
                                                <select class="form-control" name="bas_new_status">
                                                    {!! $basNewStatus !!}
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-8 col-form-label">Brand</label>
                                        <div class="col-12">
                                            <select id="brand" class="form-control" name="brand">
                                                {!! $brand !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Change Info</label>
                                        <div class="col-12">
                                            <select id="is_changeinfo" class="form-control" name="is_changeinfo">
                                                {!! $isChangeInfo !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group row">
                                        <label class="col-12 col-form-label">Opt</label>
                                        <div class="col-12">
                                            <select id="is_add_otp" class="form-control" name="is_add_otp">
                                                {!! $isUpdateOtp !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Space for Visual Balance -->
                    <div class="mt-4">&nbsp;</div>
                </div>
            </div>

            <div class="d-flex justify-content-start mt-4 ">
                <button id="btnSearch" type="submit" class="btn btn-primary mr-3">
                    <i class="fas fa-filter mr-2"></i> Apply filter
                </button>
                <a href="/channelmanagement/v2" class="btn btn-outline mr-3" id="resetBtn">
                    <i class="fas fa-undo mr-2"></i> Reset
                </a>
            </div>

        </form>

    </div>

    <form id="formChannel" class="form-horizontal form-label-left w-100" method="POST">
        {{ csrf_field() }}

        <div id="actionPanel" class="action-panel">
            <div class="action-panel-header">
                <button id="toggleActionPanelBtn" class="btn-circle-cus2">
                    <i class="fas fa-chevron-right"></i>
                </button>
                <div class="text-center">
                    <h5 class="mb-0">Channel Actions</h5>
                    <div class="selected-counter mr-2 font-14"><span id="selectedCount"></span><span> channel
                            selected</span></div>
                </div>
                <div class="d-flex align-items-center">
                    <!--<div class="selected-counter mr-2" id="selectedCount">0</div>-->
                    <button id="closeActionPanelBtn" class="btn-circle-cus2" title="Close panel">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
            <div class="action-panel-body div_scroll_50">
                <div class="action-search">
                    <input type="text" class="form-control" id="actionSearch" placeholder="Search actions...">
                </div>

                <div id="actionGroups">
                    <!-- Channel Management -->
                    <div class="action-group" data-category="management">
                        <div class="action-group-title">
                            <i class="fas fa-users"></i> Channel Management
                        </div>
                        <div class="action-btn-group">
                            @if ($is_admin_music)
                                <div class="action-item">
                                    <button class="btn btn-sm btn-outline-secondary action-btn" data-value="2"
                                        data-requires-form="true" data-form="moveChannelForm">Move Channel</button>
                                    <div id="moveChannelForm" class="action-form">
                                        <h6 class="mb-3">Move Channel to User</h6>
                                        <div class="form-group">
                                            <select id="targetUser" class="search_select" name="action_user"
                                                data-show-subtext="true" data-live-search="true" data-size="5"
                                                data-container="body" data-width="100%">
                                                {!! $listusercode !!}
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <div class="action-item">
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="1"
                                    data-requires-form="true" data-form="addGroupForm">Add to Group</button>
                                <div id="addGroupForm" class="action-form">
                                    <h6 class="mb-3">Add Channel to Group <span class="btn_add_group_channel"><i
                                                class="fa fa-plus-circle color-red" style="font-size: 20px;"></i></span>
                                    </h6>
                                    <div class="form-group">
                                        <select id="targetGroup" class="select_group_channel search_select"
                                            name="action_group_channel" data-show-subtext="true" data-live-search="true"
                                            data-size="5" data-container="body" data-width="100%">
                                            {!! $group_channel_search !!}
                                        </select>

                                    </div>
                                </div>
                            </div>
                            <div class="action-item">
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="17"
                                    data-requires-form="true" data-form="configForm">Config</button>
                                <div id="configForm" class="action-form">
                                    <h6 class="form-section-title">Configuration Settings</h6>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="profileName">Channel Id <span
                                                    class="font-13">(Youtube)</span></label>
                                            <input id="config_channel_id" class="form-control" type="text"
                                                name="config_channel_id">
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="genre">Genre</label>
                                            <select class="form-control " id="genre" name="channel_genre">
                                                {!! $channelGenre !!}
                                            </select>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label for="channelTags">Channel Tags</label>
                                            <select class="form-control" id="channelTags" multiple name="tags[]"
                                                style="height: 110px">
                                                {!! $channelTags !!}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="profileName">Profile Name</label>
                                            <input id="profile_name" class="form-control" type="text"
                                                name="profile_name" placehoder="Name show on Mooonshots">
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label for="otp_key">OTP Key</label>
                                            <input id="otp_key" class="form-control" type="text" name="otp_key"
                                                value="">
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="alarm">Alarm</label>
                                            <select id="upload_alert" name="upload_alert" class="form-control">
                                                <option selected value="-1">Select</option>
                                                <option value="1">Get Alarm</option>
                                                <option value="0">Do not get alarm</option>
                                            </select>
                                        </div>
                                        @if ($request->is_admin_music)
                                            <div class="col-12 mb-2">
                                                <label for="expire_get_pass">Date Get Pass</label>
                                                <select class="form-control" id="expire_get_pass" name="expire_get_pass">
                                                    <option value="-1">Select</option>
                                                    <option value="5">5 days</option>
                                                    <option value="4">4 days</option>
                                                    <option value="3">5 days</option>
                                                    <option value="2">2 days</option>
                                                    <option value="1">1 day</option>
                                                </select>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="row">
                                        <div class="col-12 mb-2">
                                            <label for="uploadBy">Upload By</label>
                                            <select class="form-control" id="uploadBy" name="version">
                                                <option value="-1">Select</option>
                                                <option value="1">Moonshots</option>
                                                <option value="2">API</option>
                                            </select>
                                        </div>
                                        <div class="col-12 mb-2">
                                            <label for="channel_chacking">Channel Tracking</label>
                                            <select class="form-control" id="channel_chacking" name="sub_tracking">
                                                <option value="-1">Select</option>
                                                <option value="0">No</option>
                                                <option value="1">Yes</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="action-item">
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="27"
                                    data-requires-form="true" data-form="changeIpForm">Set IP</button>
                                <div id="changeIpForm" class="action-form">
                                    <div class="form-group">
                                        <select class="form-control" name="client_27">
                                            <option value="dev2-new">dev2-new</option>
                                            <option value="client_led">client_led</option>
                                            <option value="linux_bas_v2">linux_bas_v2</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="3">Check
                                Views</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="30">Create
                                Channel</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="38">Delete
                                Channel</button>
                        </div>
                    </div>
                    <!-- Settings -->
                    <div class="action-group" data-category="settings">
                        <div class="action-group-title">
                            <i class="fas fa-cogs"></i> Settings
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="32">Change
                                Pass</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="35">Change
                                Info</button>
                        </div>
                    </div>

                    <!-- Upload Settings -->
                    <div class="action-group" data-category="upload">
                        <div class="action-group-title">
                            <i class="fas fa-upload"></i> Upload Settings
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="7">Set Upload
                                Manual</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="8">Set Upload
                                Auto</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="18">Set API Upload
                                Auto</button>
                        </div>
                    </div>

                    <!-- Comment Settings -->
                    <div class="action-group" data-category="comments">
                        <div class="action-group-title">
                            <i class="fas fa-comments"></i> Comment Settings
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="36">Comment: Turn
                                Off</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="37">Comment: Turn
                                On</button>
                        </div>
                    </div>

                    <!-- Wake Up -->
                    <div class="action-group" data-category="wakeup">
                        <div class="action-group-title">
                            <i class="fas fa-sync-alt"></i> Wake Up
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="9">Set Manual</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="10">Set Auto</button>
                        </div>
                    </div>

                    <!-- Promotion -->
                    <div class="action-group" data-category="promotion">
                        <div class="action-group-title">
                            <i class="fas fa-bullhorn"></i> Promotion
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="11">Disable
                                Cross</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="12">Enable
                                Cross</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="13">Enable Promos
                                Lyric</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="14">Disable Promos
                                Lyric</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="15">Enable Promos
                                Mix</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="16">Disable Promos
                                Mix</button>
                        </div>
                    </div>

                    <!-- Hub Settings -->
                    <div class="action-group" data-category="hub">
                        <div class="action-group-title">
                            <i class="fas fa-project-diagram"></i> Hub Settings
                        </div>
                        <div class="action-btn-group">
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="20">Hub: Turn
                                Off</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="21">Hub: Turn
                                On</button>
                        </div>
                    </div>

                    <!-- Moonshots -->

                    <div class="action-group" data-category="moonshots">
                        <div class="action-group-title">
                            <i class="fas fa-moon"></i> Moonshots
                        </div>
                        <div class="action-btn-group">
                            @if ($is_admin_music)
                                <div class="action-item">
                                    <button class="btn btn-sm btn-outline-secondary action-btn" data-value="24"
                                        data-requires-form="true" data-form="autoLoginForm">Auto Login</button>
                                    <div id="autoLoginForm" class="action-form">
                                        <div class="form-group">
                                            <label for="loginIp">Ip</label>
                                            <select id="loginIp" class="form-control" name="client">
                                                <option value="linux_bas_v2">linux_bas_v2</option>
                                                <option value="dev2-new">dev2-new</option>
                                                <option value="client_led">client_led</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="loginProxy">Proxy</label>
                                            <select id="loginProxy" class="form-control" name="proxy">
                                                <option value="">None</option>
                                                <option value="tinsoft">Tinsoft</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="26">Clear
                                Profile Moonshots</button>
                            @if ($is_admin_music)
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="29">Clear Profile
                                Bas</button>
                            @endif
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="28">Sync
                                Cookie</button>
                        </div>
                    </div>

                    <!-- Special Features -->
                    <div class="action-group" data-category="special" style="margin-bottom: 50px">
                        <div class="action-group-title">
                            <i class="fas fa-rocket"></i> Special Features
                        </div>
                        <div class="action-btn-group">
                            @if ($is_admin_music)
                                <div class="action-item">
                                    <button class="btn btn-sm btn-outline-secondary action-btn" data-value="19"
                                        data-requires-form="true" data-form="vipRender">VIP
                                        Render</button>
                                    <div id="vipRender" class="action-form">
                                        <!--<h6 class="mb-3">Add Channel to Group</h6>-->
                                        <div class="form-group">
                                            <select class="search_select" data-show-subtext="true"
                                                data-live-search="true" data-size="5" data-container="body"
                                                data-width="100%" name="vip_day">
                                                <option value="1">1 day</option>
                                                <option value="2">2 days</option>
                                                <option value="3">3 days</option>
                                                <option value="4">4 days</option>
                                                <option value="5">5 days</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            @endif
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="22">Boom VIP
                                Active</button>
                            <button class="btn btn-sm btn-outline-secondary action-btn" data-value="23">Boom VIP
                                Inactive</button>
                            @if ($is_admin_music)
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="33">YT Device OAuth</button>
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="34">Add to Social</button>
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="39">Set Cant Resolved Change Info</button>
                                <button class="btn btn-sm btn-outline-secondary action-btn" data-value="40">Set Sent user to check Change Info</button>
                            @endif
                        </div>
                    </div>
                </div>


            </div>
            <div class="action-panel-footer">
                <div class="row">
                    <div class="col-6 pr-1">
                        <button id="executeBtn" class="btn btn-primary execute-btn" data-reload="false" disabled>
                            <i class="fas fa-play-circle"></i> Execute
                        </button>
                    </div>
                    <div class="col-6 pl-1">
                        <button id="executeReloadBtn" class="btn btn-success execute-btn" data-reload="true" disabled>
                            <i class="fas fa-sync"></i> Execute & Reload
                        </button>
                    </div>
                </div>
                <input type="hidden" id="actionValue" name="action" value="">
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <div class="d-flex flex-wrap justify-content-between align-items-center ">
                    <div class="row w-50p">
                        <div class="header-search-container col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control search-input" id="search_channel_header"
                                    value="{{ $request->c1 }}" placeholder="Search ID, name or email..."
                                    onkeyup="autoSubmitSearch(event, this)">
                                <div class="search-icon">
                                    <i class="fas fa-search text-muted"></i>
                                </div>
                            </div>
                        </div>
                        @if ($is_admin_music)
                            <div class="col-md-3 btn-100">
                                <select id="user_search_header" class="form-control search_select" name="c5"
                                    data-show-subtext="true" data-live-search="true" data-size="5"
                                    data-container="body" data-width="100%">
                                    {!! $listusercode !!}
                                </select>
                            </div>
                        @endif
                        <div class="col-md-3 btn-100">
                            <div id="group_channel_2" class="select-dropdown-container"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-right">
                            
                                <button type="button" id="chartTrackButton" onclick="chartTrackChannels()"
                                    class="btn btn-outline-info mr-2 btn-100 position-relative" style="overflow: visible"
                                    data-toggle="tooltip" data-placement="top" data-original-title="">
                                    <i class="fas fa-chart-line"></i> Chart
                                </button>
                            
                            <button type="button"  onclick="filterUploadError(this)"
                                class="btn btn-outline-warning mr-2 btn-100 position-relative <?php echo $request->is_upload_error==1?"active":""; ?>" style="overflow: visible"
                                data-toggle="tooltip" data-placement="top"
                                data-original-title="Error upload, render">
                                <i class="fas fa-exclamation-triangle"></i> Upload 
                                @if($errorCountUpload>0)
                                    <span class="filter-badge ">{{ $errorCountUpload }}</span>
                                @endif    
                            </button>
                            <button type="button" id="errorChangeInfoBtn" onclick="filterError(this)"
                                class="btn btn-outline-warning mr-2 btn-100 position-relative <?php echo $request->is_change_info_error==1?"active":""; ?>" style="overflow: visible"
                                data-toggle="tooltip" data-placement="top"
                                data-original-title="Error change info channel">
                                <i class="fas fa-exclamation-triangle"></i> Error 
                                @if($errorCountChangeInfo>0)
                                    <span class="filter-badge ">{{ $errorCountChangeInfo }}</span>
                                @endif    
                            </button>
                            <button type="button" id="showFilterBtn"
                                class="btn btn-outline-primary mr-2 btn-100 position-relative" style="overflow: visible">
                                <i class="fas fa-filter mr-1"></i> Advanced
                            </button>
                            <button type="button" class="btn btn-outline-success btn-add-channel mr-2 btn-100"
                                data-toggle="tooltip" data-placement="top" data-original-title="Add Channel">
                                <i class="fas fa-plus mr-1"></i> Channel
                            </button>
                            <button type="button" class="btn btn-outline-info btn-create-channel btn-100"
                                data-toggle="tooltip" data-placement="top" data-original-title="Create Email">
                                <i class="fas fa-envelope mr-1"></i> Email
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th style="width: 5%;text-align: center">
                                    <div class="checkbox checkbox-primary tbl-chk">
                                        <input id="select_all" type="checkbox" name="select_all">
                                        <label for="select_all" class="m-b-22 p-l-0" style="margin-bottom: 1rem"></label>
                                    </div>
                                </th>
                                <?php $count = number_format($datas->total(), 0, ',', '.'); ?>
                                <th class="th-channel" colspan="4">@sortablelink('id', "Channel Details ($count)")</th>
                                <th class="th-tags">Tags</th>
                                <th class="th-increase">@sortablelink('increasing', 'Growth')</th>
                                <th class="th-views">@sortablelink('view_count', 'Views')</th>
                                <th class="th-hub">Hub</th>
                                <th class="th-created text-center">@sortablelink('chanel_create_date', 'Date Created')</th>
                                <th class="th-subs">@sortablelink('subscriber_count', 'Subs')</th>
                                <th class="th-start">@sortablelink('confirm_time', 'Start Date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($datas as $data)
                                <tr id="channel-{{ $data->id }}" class="channel-row" data-id="{{ $data->id }}">
                                    <td class="text-center">
                                        <div class="checkbox checkbox-primary tbl-chk">
                                            <input class="checkbox-multi" type="checkbox" name="chkChannelAll[]"
                                                id="ck-video<?php echo $data->id; ?>" value="{{ $data->id }}">
                                            <label class="m-b-18 p-l-0" for="ck-video<?php echo $data->id; ?>"></label>
                                        </div>
                                    </td>
                                    <?php
                                    $brand = 'Brand';
                                    $disable = '';
                                    $color = '';
                                    $tooltip = '';
                                    if ($data->is_rebrand == 1) {
                                        $brand = 'Branding';
                                        $disable = 'disabled';
                                        $color = 'not-branded';
                                        $tooltip = "Running bas_id $data->bas_id";
                                    } elseif ($data->is_rebrand == 5) {
                                        $brand = 'Branded';
                                        $disable = '';
                                        $color = 'branded';
                                        $tooltip = 'Brand successful';
                                    } elseif ($data->is_rebrand == 4) {
                                        $brand = 'Brand Err';
                                        $disable = '';
                                        $color = 'brand-error';
                                        $tooltip = "$data->bas_id $data->gmail_log";
                                    } elseif ($data->is_rebrand == 0) {
                                        $tooltip = 'Brand this channel';
                                    }
                                    ?>

                                    <td colspan="4">
                                        <div class="d-flex align-items-center">
                                            <div class="channel-avatar-v1 position-relative {{ $color }}">
                                                <img data-id="{{ $data->id }}" src="{{ $data->channel_clickup }}"
                                                    class="rounded-circle" alt="Channel Avatar"
                                                    onerror="this.src='/images/default-avatar.png'">

                                                <!-- Edit button overlay -->
                                                <button type="button" class="avatar-sync-btn cur-poiter"
                                                    value="{{ $data->id }}">
                                                    <i class="fas fa-sync-alt"></i>
                                                </button>
                                                <button type="button" class="avatar-edit-btn cur-poiter btn-brand"
                                                    value="{{ $data->id }}" channelName="{{ $data->chanel_name }}"
                                                    data-toggle="tooltip" data-placement='bottom'
                                                    title="{{ $tooltip }}">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                            </div>

                                            <div class="channel-info">
                                                <div class="channel-name-container">
                                                    <h5 class="mb-0 d-flex align-items-center">
                                                        <span id="channel_name_{{ $data->id }}"
                                                            class="channel-name copyable-channel <?php echo $data->status == 0 ? 'song-block' : ''; ?>"
                                                            data-channel-id="https://www.youtube.com/channel/{{ $data->chanel_id }}">{{ $data->chanel_name }}</span>
                                                        @if ($data->epid_status == 'approved')
                                                            <i class="fas fa-check-circle text-success ml-1"
                                                                data-toggle="tooltip" title="Epid channel"></i>
                                                        @elseif($data->epid_status == 'admin_rejected' || $data->epid_status == 'rejected')
                                                            <i class="fas fa-check-circle text-danger ml-1"
                                                                data-toggle="tooltip" title="Epid Rejected"></i>
                                                        @elseif($data->epid_status == 'pending' || $data->epid_status == 'sent_to_epid')
                                                            <i class="fas fa-check-circle text-warning ml-1"
                                                                data-toggle="tooltip" title="Waiting for response"></i>
                                                        @endif
                                                    </h5>
                                                </div>
                                                <div class="channel-details mt-2">
                                                    <div class="detail-row d-flex text-muted">
                                                        <div class="detail-item mr-3">
                                                            <i class="fas fa-hashtag fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->id }}">{{ $data->id }}</span>
                                                        </div>
                                                        <div class="detail-item mr-3"  id="email-container-{{ $data->id }}">
                                                            <i class="fas fa-user fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ substr($data->user_name, 0, strripos($data->user_name, '_')) }}">{{ substr($data->user_name, 0, strripos($data->user_name, '_')) }}</span>
                                                        
                                                            @if($data->reco_email != null)
                                                                <div class="recovery-email-container detail-item ml-2" data-id="{{$data->id}}">
                                                                    <i class="fas fa-shield-alt text-muted mr-2"></i>
                                                                    <span class="copyable-text" data-copy="{{$data->reco_email}}">{{$data->reco_email}}</span>
                                                                </div>
                                                                <button type="button" 
                                                                        class="btn-finish-recovery cur-poiter" 
                                                                        data-id="{{ $data->id }}"
                                                                        onclick="handleFinishRecovery({{ $data->id }})"
                                                                        data-toggle="tooltip" 
                                                                        title="Change Recovery Email to {{ $data->reco_email }} completed">
                                                                    <i class="fas fa-rocket"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="detail-row d-flex text-muted">
                                                        <div class="detail-item mr-3">
                                                            <i class="fas fa-at fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->handle }}">{{ $data->handle }}</span>
                                                        </div>
                                                        <?php
                                                        $gCountText = '';
                                                        foreach ($gmailCounts as $gCount) {
                                                            if ($data->note == $gCount->note) {
                                                                $gCountText = "<span class='gmail-count'>$gCount->total</span>";
                                                                break;
                                                            }
                                                        }
                                                        ?>
                                                        <div class="detail-item">
                                                            <i class="fas fa-envelope fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->note }}">{{ $data->note }}</span>
                                                            {!! $gCountText !!}

                                                        </div>
                                                    </div>
                                                    <div class="detail-row d-flex text-muted">
                                                        <div class="detail-item">
                                                            <?php
                                                            $groupName = '';
                                                            if (isset($listGroupChannel)) {
                                                                foreach ($listGroupChannel as $groupChannel) {
                                                                    if ($groupChannel->id == $data->group_channel_id) {
                                                                        $groupName = $groupChannel->group_name;
                                                                        break;
                                                                    }
                                                                }
                                                            }
                                                            ?>
                                                            <i class="fas fa-users fa-fw text-muted mr-2"></i>
                                                            <a
                                                                href="/channelmanagement/v2?c3={{ $data->group_channel_id }}">{{ $groupName }}</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="action-buttons mt-3">
                                                    <button type="button" class="btn btn-sm btn-action btn-gologin"
                                                        data-toggle="tooltip" data-profile="{{ $data->gologin }}"
                                                        value="{{ $data->hash_pass }}"
                                                        title="Login to account {{ $data->gologin }}">
                                                        <i class="fas fa-sign-in-alt"></i> Login
                                                    </button>
                                                    <a id="commit-{{ $data->hash_pass }}"
                                                        data-mail="{{ $data->note }}" target="_blank"
                                                        href="AutoProfile://profile/commit/?id={{ $data->gologin }}&gmail={{ $data->note }}&force=1"
                                                        class="btn btn-sm btn-action" data-toggle="tooltip"
                                                        title="Commit Moonshots">
                                                        <i class="fas fa-rocket"></i> Commit
                                                    </a>

                                                    @if ($data->otp_key != null)
                                                        <button type="button" data-toggle="tooltip"
                                                            title="Get login code"
                                                            class="btn-getcode cur-poiter btn btn-sm btn-action"
                                                            value="{{ $data->hash_pass }}"><i
                                                                class="fas fa-key mr-2"></i> Code</button>
                                                    @endif
                                                    @if ($data->chanel_id == null || App\Common\Utils::containString($data->chanel_id, '@'))
                                                        <button id="btn-add-channel-id-{{ $data->id }}"
                                                            type="button" class="btn btn-sm btn-action"
                                                            data-toggle="tooltip" title="Add channel ID"
                                                            style="color:red"
                                                            onclick="showChannelIdInput({{ $data->id }})">
                                                            <i class="fas fa-plus-circle"></i> Add ID
                                                        </button>
                                                    @endif
                                                    @if ($data->otp_key == null || $data->is_add_otp == 0)
                                                        <button id="btn-insert-otp-{{ $data->id }}" type="button"
                                                            class="btn btn-sm btn-action" data-toggle="tooltip"
                                                            title="Add OTP code" style="color:red"
                                                            onclick="insertOtpKey({{ $data->id }})">
                                                            <i class="fas fa-key"></i> Add OTP
                                                        </button>
                                                    @endif
                                                    <button type="button"
                                                        class="btn btn-sm btn-action btn-recheck-channel"
                                                        data-toggle="tooltip" value="{{ $data->id }}"
                                                        title="Check channel views">
                                                        <i class="fas fa-eye"></i> Check
                                                    </button>
                                                    <button type="button"
                                                        class="btn btn-sm btn-action view-realtime-chart cur-poiter"
                                                        data-channel='{{ $data->chanel_id }}'
                                                        data-name='{{ $data->chanel_name }}'><i
                                                            class="fas fa-chart-line mr-2"></i> Chart</button>
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button"
                                                            class="btn btn-sm btn-action dropdown-toggle" type="button"
                                                            data-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-h"></i> More
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            <button type="button"
                                                                class="dropdown-item cur-poiter track-channel-btn"
                                                                data-channel-id="{{ $data->id }}"></button>
                                                            @if ($data->epid_status == null)
                                                                <button type="button" class="dropdown-item cur-poiter"
                                                                    onclick="submitEpid({{ $data->id }})"><i
                                                                        class="fas fa-music mr-2"></i> Submit
                                                                    Epidemic</button>
                                                            @endif
                                                             @if ($data->reco_email == null && 
                                                                    ($data->last_change_pass==4 || $data->last_change_pass==6 || $data->last_change_pass==7))
                                                                <button type="button" class="dropdown-item cur-poiter btn-get-recovery-email" value="{{ $data->hash_pass }}" data-id="{{ $data->id }}">
                                                                <i class="fas fa-envelope mr-2"></i> New Recovery Email
                                                                </button>
                                                             @endif
                                                             @if ($data->reco_email != null)
                                                                <button type="button" class="dropdown-item cur-poiter btn-getcode-change-recovery" 
                                                                        value="{{ $data->hash_pass }}" data-id="{{ $data->id }}"
                                                                        onclick="getCodeRecoveryForChangeRecovery(this)">
                                                                <i class="fas fa-code mr-2"></i> Code to Change Recovery
                                                                </button>
                                                             @endif
                                                            @if ($data->otp_key != null)
                                                                <button type="button"
                                                                    class="dropdown-item btn-getcode-recovery cur-poiter"
                                                                    value="{{ $data->hash_pass }}"><i
                                                                        class="fas fa-undo mr-2"></i> Get Recovery
                                                                    Code</button>
                                                            @endif
                                                            <a class="dropdown-item copyable-channel"
                                                                data-channel-id="{{ $data->gologin }}"><i
                                                                    class="fas fa-moon mr-2"></i> Copy Profile ID</a>
                                                            <div class="dropdown-divider"></div>
                                                            <a class="dropdown-item text-danger cur-poiter"
                                                                onclick="deleteChannel({{ $data->id }})"><i
                                                                    class="fas fa-trash-alt mr-2"></i> Delete</a>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="tags-container">
                                            @foreach ($data->tags_array as $tag)
                                                @if ($tag != null)
                                                    <span
                                                        class="badge badge-primary d-block mb-1 cur-poiter position-relative">
                                                        {{ $tag }}
                                                        <i class="fas fa-times tag-delete"></i>
                                                    </span>
                                                @endif
                                            @endforeach
                                            <button type="button"
                                                class="btn btn-sm btn-outline-secondary add-tag-btn mt-2">
                                                <i class="fas fa-plus-circle"></i> Add Tag
                                            </button>
                                        </div>
                                    </td>
                                    <td id="growth-views-{{ $data->id }}">
                                        <span>
                                            {{ number_format($data->increasing, 0, ',', '.') }}
                                            @if ($data->inc_percent >= 0)
                                                <i class="fas fa-arrow-up growth-arrow text-success"></i>
                                                <span
                                                    class="growth-percentage text-success">{{ $data->inc_percent }}%</span>
                                            @else
                                                <i class="fas fa-arrow-down growth-arrow text-danger"></i>
                                                <span
                                                    class="growth-percentage text-danger">{{ $data->inc_percent }}%</span>
                                            @endif
                                        </span>
                                    </td>
                                    <td id="total-views-{{ $data->id }}">
                                        <div>{{ number_format($data->view_count, 0, ',', '.') }}</div>
                                        <div class="small text-muted">{{ $data->inc_time }}</div>
                                    </td>
                                    <td>
                                        @if ($data->turn_off_hub)
                                            <span class="hub-status text-danger">Off</span>
                                        @else
                                            <span class="hub-status text-success">On</span>
                                        @endif
                                    </td>
                                    <td style="width:160px">
                                        <?php
                                        echo $data->chanel_create_date != null ? '<div>' . gmdate('Y/m/d', $data->chanel_create_date + $user_login->timezone * 3600) . '</div>' : '';
                                        ?>
                                        <?php
                                            //4: lỗi, 6: không thể giải quyết,7:cần bassteam giải quyết
//                                            $errorMessage = $data->message;
//
//                                            // Split the error message into lines
//                                            $errorLines = preg_split('/\r\n|\r|\n/', $errorMessage);
//
//                                            // First line is the title/header, rest is the detail
//                                            $errorTitle = isset($errorLines[0]) ? $errorLines[0] : 'Error';
//
//                                            // Combine remaining lines as details (if any)
//                                            $errorDetail = '';
//                                            if (count($errorLines) > 1) {
//                                                unset($errorLines[0]);
//                                                $errorDetail = implode("\n", $errorLines);
//                                            }
                                        ?>
                                        @if ($data->last_change_pass > 7)
                                            <div class="small text-muted">{{ $data->message == null ? 'Change info' : $data->message }}</div>
                                            <div class="small text-muted">{{ App\Common\Utils::calcTimeText($data->last_change_pass) }}</div>
                      
                                        @elseif($data->last_change_pass == 4 || $data->last_change_pass == 6 || $data->last_change_pass == 7)
                                            <div class="error-container position-relative" data-id="{{ $data->id }}">
                                                    <div class="error-actions ml-auto">
                                                        <button class="error-action-btn btn-resolve-error" data-action-type="resolve" title="Mark as resolved">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        @if($is_admin_music && $data->last_change_pass != 7)
                                                            <button class="error-action-btn btn-check-error" data-action-type="check" title="Send user to check">
                                                                <i class="fas fa-user-check"></i>
                                                            </button>
                                                        @endif
                                                        @if($data->last_change_pass != 6)
                                                            <button class="error-action-btn btn-remove-error" data-action-type="not_resolve" title="Mark as cant resolved">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                <div class="error-display d-flex align-items-center">
                                                    <span class="error-text text-danger">{{ $data->message }}</span>
                                                </div>
                                                @if (!empty($data->message))
                                                    <div class="error-details d-none">{{ $data->message }}</div>
                                                @endif
                                            </div>
                                        @endif
                                        @if ($data->last_upload !== null && $data->status_upload==3)
                                            @php
                                                $err = json_decode($data->last_upload);
                                                $count = !empty($err[0]->count) ?  ("(".$err[0]->count.")") : "";
                                            @endphp

                                            @if (!empty($err[0]->error_message))
                                                <div class="error-container position-relative" data-id="{{ $data->id }}">
                                                    <div class="error-actions ml-auto">
                                                        <button class="error-action-btn btn-resolve-error" data-action-type="resolve" data-error-type="upload" title="Mark as resolved">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                        @if($data->status_upload != 4)
                                                            <button class="error-action-btn btn-remove-error" data-action-type="not_resolve" data-error-type="upload" title="Mark as cant resolved">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        @endif
                                                    </div>
                                                    <div class="error-display d-flex align-items-center">
                                                        <span class="error-text text-danger">{{$count}} • {{ $err[0]->error_message }}</span>
                                                    </div>
                                                    <div class="error-details d-none">job {{$err[0]->job_id}} - {{  $err[0]->error_message }} {{$count}}</div>
                                                </div>
                                            @endif
                                        @endif
                                    </td>
                                    <td>{{ number_format($data->subscriber_count, 0, ',', '.') }}</td>
                                    <td>
                                        @if ($data->confirm_time != null)
                                            {{ App\Common\Utils::convertToViewDate($data->confirm_time) }}
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                <div class="records-info">
                    <?php
                    $info = str_replace('_START_', $datas->firstItem() != null ? $datas->firstItem() : '0', trans('label.title.sInfo'));
                    $info = str_replace('_END_', $datas->lastItem() != null ? $datas->lastItem() : '0', $info);
                    $info = str_replace('_TOTAL_', $datas->total(), $info);
                    echo $info;
                    ?>
                </div>
                <div class="d-flex align-items-center">
                    <div class="records-per-page mr-3">
                        <select id="cbbLimit" name="limit" class="form-control">
                            {!! $limitSelectbox !!}
                        </select>
                    </div>
                    @if (isset($datas))
                        {!! $datas->links() !!}
                    @endif
                </div>
            </div>
        </div>


        @include('dialog.card_endscreen')
    </form>

    @include('dialog.card_management')
    @include('dialog.groupchannel')
    @include('dialog.brand')
    @include('dialog.realtimeviews')
    @include('dialog.description_edit')
    @include('dialog.channel.channeladd')
    @include('dialog.channel.channelcreate')
    @include('dialog.channel.multichartrealtime')
@endsection

@section('script')
    <script type="text/javascript">
        
    function handleFinishRecovery(channelId) {
        var button = $('.btn-finish-recovery[data-id="' + channelId + '"]');

        button.tooltip('hide');
        button.html('<i class="fa fa-circle-o-notch fa-spin"></i>');
        button.prop('disabled', true);

        $.ajax({
            type: "POST",
            url: "finishRecoveryEmail",
            data: {
                "_token": "{{ csrf_token() }}",
                "id": channelId
            },
            dataType: 'json',
            success: function(data) {
                if (data.status == "success") {
                    button.fadeOut(300, function() {
                        $(this).remove();
                    });
                    showNotification("Recovery email has been confirmed", "success");
//                    location.reload();
                } else {
                    button.html('<i class="fas fa-shield-alt"></i>');
                    button.prop('disabled', false);
                    showNotification(data.message || "Failed to confirm recovery email", "error");
                }
            },
            error: function(data) {
                button.html('<i class="fas fa-shield-alt"></i>');
                button.prop('disabled', false);
                showNotification("Error confirming recovery email", "error");
                console.log('Error:', data);
            }
        });
    }        
        $('.btn-get-recovery-email').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var hashPass = $(this).val();
            var channelId = $(this).data('id');
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';

            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }

            $.ajax({
                type: "GET",
                url: "getRecoveryEmail",
                data: {
                    "id": channelId,
                    "hash": hashPass
                },
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));

                    if (data.status == "success") {
                        // Copy email to clipboard
                        copyToClipboard2(data.email);
                        showNotification("Recovery email copied: " + data.email, "success");

                        // Add Finish button after the email container
                        var emailContainer = $('#email-container-' + channelId);

                        $('.recovery-email-container[data-id="' + channelId + '"]').remove();
                        $('.btn-finish-recovery[data-id="' + channelId + '"]').remove();
                        emailContainer.append(
                             `<div class="recovery-email-container detail-item ml-2" data-id="${channelId}">
                                 <i class="fas fa-shield-alt text-muted mr-2"></i>
                                 <span class="copyable-text" data-copy="${data.email}">${data.email}</span>
                              </div>`
                         );
                        emailContainer.append(
                            `<button type="button" 
                             class="btn-finish-recovery cur-poiter" 
                             data-id="${channelId}" 
                             onclick="handleFinishRecovery(${channelId})"
                             data-toggle="tooltip" 
                             title="Change Recovery Email to ${data.email} completed ">
                                <i class="fas fa-rocket"></i>
                             </button>`
                        );
                 
                        $('.btn-finish-recovery[data-id="' + channelId + '"]').tooltip(); 

                        var changeRecoveryButton = $('.btn-getcode-change-recovery[data-id="' + channelId + '"]');
                        if (changeRecoveryButton.length === 0) {
                            // Thêm nút mới vào sau nút hiện tại trong dropdown
                            $this.after(
                                `<button type="button" class="dropdown-item cur-poiter btn-getcode-change-recovery" 
                                    value="${hashPass}" data-id="${channelId}" 
                                    onclick="getCodeRecoveryForChangeRecovery(this)">
                                    <i class="fas fa-code mr-2"></i> Code to Change Recovery
                                </button>`
                            );
                        }
                    } else {
                        showNotification(data.message || "Failed to get recovery email", "error");
                    }
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                    showNotification("Error getting recovery email", "error");
                    console.log('Error:', data);
                }
            });
        }); 
        function getCodeRecoveryForChangeRecovery(button) {
            var $button = $(button);
            var hashPass = $button.val();
            var channelId = $button.data('id');
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';

            if ($button.html() !== loadingText) {
                $button.data('original-text', $button.html());
                $button.html(loadingText);
            }

            $.ajax({
                type: "GET",
                url: "getCodeRecoveryForChangeRecovery",
                data: {
                    "id": channelId,
                    "hash": hashPass
                },
                dataType: 'json',
                success: function(data) {
                    $button.html($button.data('original-text'));

                    if (data.status == "success") {
                        // Copy code to clipboard
                        copyToClipboard2(data.code);
                        showNotification(`Code ${data.code} copied to clipboard`, "success");
                    } else {
                        showNotification(data.message || "Failed to get recovery change code", "error");
                    }
                },
                error: function(data) {
                    $button.html($button.data('original-text'));
                    showNotification("Error getting recovery change code", "error");
                    console.log('Error:', data);
                }
            });
        }
        
        // Handle hover effect for error container
        $(document).on('mouseenter', '.error-container', function() {
            $(this).find('.error-details').removeClass('d-none');
        }).on('mouseleave', '.error-container', function() {
            $(this).find('.error-details').addClass('d-none');
        });

        $(document).on('click', '.error-action-btn', function(e) {
            e.stopPropagation();
            e.preventDefault();

            var $this = $(this);
            const channelId = $(this).closest('.error-container').data('id');
            const errorContainer = $(this).closest('.error-container');
            const actionType = $(this).data('action-type');
            const errorType = $(this).data('error-type');

            // Get current error details (if any)
            const errorDetail = errorContainer.find('.error-details').text().trim();

            // Show input dialog for not_resolve and check actions
            if (actionType === 'not_resolve' || actionType === 'check') {
                // Configure dialog based on action type
                let title = actionType === 'not_resolve' ? 
                    'Mark Error as Cannot Be Resolved' : 
                    'Send to User for Checking';

                // Use $.confirm to display input dialog
                $.confirm({
                    title: title,
                    content: '' +
                    '<form action="" class="formName">' +
                    '<div class="form-group">' +
                    '<label>Enter message</label>' +
                    '<textarea class="message form-control" style="line-height:1.25" rows="5">' + errorDetail + '</textarea>' +
                    '</div>' +
                    '</form>',
                    buttons: {
                        confirm: {
                            text: 'Confirm',
                            btnClass: 'btn-blue',
                            action: function () {
                                var message = this.$content.find('.message').val();
                                processErrorAction($this, channelId, errorContainer, actionType, message,errorType);
                            }
                        },
                        cancel: {
                            text: 'Cancel'
                        }
                    }
                });
            } else {
                // For 'resolve' action, proceed immediately without dialog
                processErrorAction($this, channelId, errorContainer, actionType, errorDetail,errorType);
            }
        });

        // Function to process action after input is provided
        function processErrorAction($button, channelId, errorContainer, actionType, message,errorType="") {
            // Show loading state
            $button.html('<i class="fas fa-spinner fa-spin"></i>');

            // Call API to handle the message
            $.ajax({
                url: '/ajaxChannel',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    action: 17,
                    action_type: actionType,
                    error_type: errorType,
                    chkChannelAll: [channelId],
                    message: message 
                },
                success: function(response) {
                    if (actionType === 'resolve') {
                        $button.html('<i class="fas fa-check"></i>');
                    } else if (actionType === 'check') {
                        $button.html('<i class="fas fa-user-check"></i>');
                        showNotification("Success", "success");
                        return;
                    } else if (actionType === 'not_resolve') {
                        $button.html('<i class="fas fa-times"></i>');
                    }

                    // Remove error container with effect (only for resolve and not_resolve)
                    errorContainer.fadeOut(300, function() {
                        $(this).remove();
                    });

                    showNotification("Success", "success");
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Error processing message:', xhr.responseText);

                    // Restore icon based on action type
                    if (actionType === 'resolve') {
                        $button.html('<i class="fas fa-check"></i>');
                    } else if (actionType === 'check') {
                        $button.html('<i class="fas fa-user-check"></i>');
                    } else if (actionType === 'not_resolve') {
                        $button.html('<i class="fas fa-times"></i>');
                    }

                    showNotification("Failed to process error message", "error");
                }
            });
        }

        function filterError($this) {

            if($($this).hasClass("active")){
                $("#is_change_info_error").val(null);
            }else{
                $("#is_change_info_error").val(1);
            }
             $('#btnSearch').click();

//            // Lấy URL hiện tại
//            var currentUrl = window.location.href;
//
//            // Kiểm tra xem URL đã có dấu ? chưa
//            var newUrl = currentUrl.includes('?') ?
//                currentUrl + '&is_change_info_error=1' :
//                currentUrl + '?is_change_info_error=1';
//
//            // Chuyển hướng đến URL mới
//            window.location.href = newUrl;

               
        }
        function filterUploadError($this) {

            if($($this).hasClass("active")){
                $("#is_upload_error").val(null);
            }else{
                $("#is_upload_error").val(1);
            }
             $('#btnSearch').click();

//            // Lấy URL hiện tại
//            var currentUrl = window.location.href;
//
//            // Kiểm tra xem URL đã có dấu ? chưa
//            var newUrl = currentUrl.includes('?') ?
//                currentUrl + '&is_change_info_error=1' :
//                currentUrl + '?is_change_info_error=1';
//
//            // Chuyển hướng đến URL mới
//            window.location.href = newUrl;

               
        }

        function submitEpid(id) {
            $.ajax({
                type: "GET",
                url: "/channel/epid/status",
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.status == "success") {

                    }


                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        }

        $(".avatar-sync-btn").click(function() {
            const button = $(this);
            const avatarContainer = button.closest('.channel-avatar-v1');
            const img = avatarContainer.find('img');
            const channelRow = avatarContainer.closest('tr.channel-row');
            const id = channelRow.data('id');
            button.css({
                "opacity": "1"
            });
            button.html('<i class="fas fa-spinner fa-spin"></i>');
            button.prop('disabled', true);
            $.ajax({
                type: "GET",
                url: "syncAvatar",
                data: {
                    "id": id
                },
                dataType: 'json',
                success: function(data) {
                    button.css({
                        "opacity": "0"
                    });
                    button.html('<i class="fas fa-sync-alt"></i>');
                    button.prop('disabled', false);
                    if (data.status == "success") {
                        img.attr("src", data.thumb);

                    }


                },
                error: function(data) {
                    console.log('Error:', data);
                    button.html('<i class="fas fa-sync-alt"></i>');
                    button.prop('disabled', false);

                }
            });

        });

        function initTagManagement() {
            $(document).on('click', '.add-tag-btn', handleAddTagClick);
            $(document).on('click', '.tag-delete', handleDeleteTagClick);
            $(document).on('click', '.badge', handleTagClick);
        }
        initTagManagement();

        function handleAddTagClick() {
            const id = $(this).closest('tr').data('id');
            const tagsContainer = $(this).closest('.tags-container');
            console.log(id, tagsContainer);
            const tagStyles = `
                                <style>
                                .tag-suggestions-list {
                                    max-height: 200px;
                                    overflow-y: auto;
                                    background: white;
                                    border: 1px solid #ced4da;
                                    border-radius: 0.25rem;
                                    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.15);
                                    display: none;
                                }

                                .tag-suggestion-item {
                                    padding: 8px 12px;
                                    cursor: pointer;
                                    position: relative;
                                    display: flex;
                                    justify-content: space-between;
                                    align-items: center;
                                }

                                .tag-suggestion-item:hover {
                                    background-color: #f8f9fa;
                                }
                                
                                .tag-delete-btn {
                                    color: #dc3545;
                                    cursor: pointer;
                                    margin-left: 8px;
                                    opacity: 0;
                                    transition: opacity 0.2s;
                                }
                                
                                .tag-suggestion-item:hover .tag-delete-btn {
                                    opacity: 1;
                                }
                                </style>`;

            // Thêm style vào head khi function được gọi
            $('head').append(tagStyles);

            // Lấy danh sách tag từ API
            let availableTags = [];
            $.ajax({
                type: "GET",
                url: "/getTags", // URL API để lấy danh sách tag
                data: {
                    query: "" // Có thể thêm query từ input nếu cần
                },
                dataType: 'json',
                async: false, // Chờ kết quả để hiển thị dialog với danh sách đã có
                success: function(data) {
                    if (data.status == "success") {
                        availableTags = data.tags;
                    } else if (Array.isArray(data)) {
                        // Định dạng mới: [{"username":"system","tag":"tag1"},{"username":"truongpv","tag":"tag2"}]
                        availableTags = data;
                    } else {
                        showNotification("Không thể tải danh sách tag", "error");
                    }
                },
                error: function(data) {
                    console.log('Error loading tags:', data);
                }
            });

            $.confirm({
                title: "Add New Tag",
                content: `<div class="tag-autocomplete-container">
                    <input type="text" id="txt_tag_name" value="" class="form-control">
                  </div>
                  <small class="form-text text-muted">Nhập tên tag mới hoặc chọn từ danh sách</small>`,
                onContentReady: function() {
                    const $input = this.$content.find("#txt_tag_name");

                    // Tạo danh sách gợi ý NGOÀI dialog (append vào body)
                    // Để tránh bị ẩn bởi z-index của dialog
                    const $suggestions = $('<div id="tag-suggestions" class="tag-suggestions-list"></div>');
                    $('body').append($suggestions);

                    // Cập nhật vị trí của danh sách gợi ý khi hiển thị
                    function updateSuggestionPosition() {
                        const inputPos = $input.offset();
                        const inputHeight = $input.outerHeight();
                        const inputWidth = $input.outerWidth();

                        $suggestions.css({
                            position: 'absolute',
                            top: inputPos.top + inputHeight,
                            left: inputPos.left,
                            width: inputWidth,
                            zIndex: 999999999 // z-index cao hơn jQuery Confirm
                        });
                    }

                    // Hiển thị gợi ý ngay khi click vào input
                    $input.on('focus', function() {
                        updateSuggestions($(this).val());
                        updateSuggestionPosition();
                        $suggestions.show();
                    });

                    // Cập nhật gợi ý khi gõ
                    $input.on('input', function() {
                        updateSuggestions($(this).val());
                        updateSuggestionPosition();
                    });

                    // Ẩn gợi ý khi click ra ngoài
                    $(document).on('click', function(e) {
                        if (!$(e.target).closest('.tag-autocomplete-container').length &&
                            !$(e.target).closest('#tag-suggestions').length) {
                            $suggestions.hide();
                        }
                    });

                    // Hàm cập nhật danh sách gợi ý
                    function updateSuggestions(query) {
                        query = query.toLowerCase().trim();
                        let filtered = availableTags;

                        // Lọc tag theo query nếu có
                        if (query) {
                            if (Array.isArray(availableTags) && availableTags.length > 0 &&
                                typeof availableTags[0] === 'object') {
                                // Định dạng mới: [{"username":"system","tag":"tag1"},{"username":"truongpv","tag":"tag2"}]
                                filtered = availableTags.filter(item =>
                                    item.tag.toLowerCase().includes(query)
                                );
                            } else {
                                // Định dạng cũ: ["tag1", "tag2", ...]
                                filtered = availableTags.filter(tag =>
                                    tag.toLowerCase().includes(query)
                                );
                            }
                        }

                        // Hiển thị danh sách gợi ý
                        if (filtered.length > 0) {
                            $suggestions.empty();

                            filtered.forEach(item => {
                                // Xác định dữ liệu tag và username
                                let tagName, username, channelCount;
                                if (typeof item === 'object') {
                                    // Định dạng mới: {username: "...", tag: "..."}
                                    tagName = item.tag;
                                    username = item.username;
                                    channelCount = item.channel_count > 1 ? item.channel_count +
                                        ' channels' : (item.channel_count < 0 ? 0 : item
                                        .channel_count) + ' channel';
                                }

                                // Chỉ hiển thị nút xóa nếu username không phải là "system"
                                const deleteButton = username !== "system" ?
                                    `<i class="fas fa-times tag-delete-btn"></i>` : '';

                                const $item = $(`<div class="tag-suggestion-item" data-username="${username}">
                            <span class="tag-name">${tagName} <span class="text-muted font-13">${channelCount}</span></span>
                            ${deleteButton}
                        </div>`);

                                // Xử lý sự kiện click vào tag (chọn tag)
                                $item.find('.tag-name').on('click', function() {
                                    $input.val(tagName);
                                    $suggestions.hide();
                                });

                                // Chỉ xử lý sự kiện xóa nếu có nút xóa
                                if (username !== "system") {
                                    // Xử lý sự kiện click vào nút xóa
                                    $item.find('.tag-delete-btn').on('click', function(e) {
                                        e.stopPropagation(); // Ngăn sự kiện bubbling lên
                                        $('body').append(
                                            '<div id="confirm-container" style="position: relative; z-index: 999999999999;"></div>'
                                            );
                                        $.confirm({
                                            title: 'Delete Tag',
                                            content: `Are you sure you want to delete this tag? "${tagName}"?`,
                                            container: '#confirm-container',
                                            zIndex: 1,
                                            buttons: {
                                                "Cancel": function() {},
                                                "Delete": {
                                                    btnClass: "btn-danger",
                                                    action: function() {
                                                        // Gọi API xóa tag
                                                        $.ajax({
                                                            type: "GET",
                                                            url: "/deleteTag",
                                                            data: {
                                                                "tag_name": tagName
                                                            },
                                                            dataType: 'json',
                                                            success: function(
                                                                data) {
                                                                if (data
                                                                    .status ==
                                                                    "success"
                                                                    ) {
                                                                    // Xóa tag khỏi danh sách
                                                                    availableTags
                                                                        =
                                                                        availableTags
                                                                        .filter(
                                                                            t => {
                                                                                return t
                                                                                    .tag !==
                                                                                    tagName;
                                                                            }
                                                                            );
                                                                    $item
                                                                        .remove();
                                                                    // Xóa tag khỏi giao diện nếu có
                                                                    $(tagsContainer)
                                                                        .find(
                                                                            '.badge'
                                                                            )
                                                                        .each(
                                                                            function() {
                                                                                if ($(
                                                                                        this)
                                                                                    .text()
                                                                                    .trim() ===
                                                                                    tagName
                                                                                    ) {
                                                                                    $(this)
                                                                                        .remove();
                                                                                }
                                                                            }
                                                                            );
                                                                }
                                                                showNotification
                                                                    (data
                                                                        .message,
                                                                        data
                                                                        .status
                                                                        );
                                                            },
                                                            error: function(
                                                                data) {
                                                                console
                                                                    .log(
                                                                        'Error:',
                                                                        data
                                                                        );
                                                                showNotification
                                                                    ("Delete error",
                                                                        "error"
                                                                        );
                                                            }
                                                        });
                                                    }
                                                }
                                            }
                                        });
                                    });
                                }

                                $suggestions.append($item);
                            });
                            $suggestions.show();
                        } else {
                            $suggestions.hide();
                        }
                    }
                },
                onClose: function() {
                    // Xóa danh sách gợi ý khỏi DOM khi đóng dialog
                    $('#tag-suggestions').remove();
                },
                buttons: {
                    "Cancel": function() {},
                    "Save": {
                        btnClass: "btn-blue",
                        action: function() {
                            let tagName = this.$content.find("#txt_tag_name").val().trim();
                            const dialog = this;
                            $.ajax({
                                type: "GET",
                                url: "channelTag",
                                data: {
                                    "id": id,
                                    "tag_name": tagName,
                                    "action": "add"
                                },
                                dataType: 'json',
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == "success") {
                                        const newTag = $(`<span class="badge badge-primary d-block mb-1 cur-poiter position-relative">
                                    ${tagName}
                                    <i class="fas fa-times tag-delete"></i>
                                </span>`);
                                        $(tagsContainer).find('.add-tag-btn').before(newTag);
                                        dialog.close();
                                    } else {
                                        // Trường hợp lỗi
                                        let message = data.message || "Add Tag Fail";
                                        let status = data.status || "error";
                                        showNotification(message, status);
                                        return false;
                                    }
                                },
                                error: function(data) {
                                    console.log('Error:', data);
                                    return false;
                                }
                            });
                            return false;
                        }
                    }
                }
            });
        }

        function handleDeleteTagClick(e) {
            e.stopPropagation(); // Ngăn sự kiện bubbling lên thẻ cha
            const tagElement = $(this).parent();
            const tagName = tagElement.text().trim();
            const id = $(this).closest('tr').data('id');
            $.confirm({
                title: 'Delete Tag',
                content: `Are you sure to remove tag ${tagName} from this channel?`,
                buttons: {
                    confirm: {
                        text: 'Delete',
                        btnClass: 'btn-danger',
                        action: function() {
                            $.ajax({
                                type: "GET",
                                url: "channelTag",
                                data: {
                                    "id": id,
                                    "tag_name": tagName,
                                    "action": "delete"
                                },
                                dataType: 'json',
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == "success") {
                                        tagElement.fadeOut(300, function() {
                                            $(this).remove();
                                        });
                                    } else {
                                        //                                        $.alert(data.message);
                                        showNotification(data.message, data.status);

                                    }
                                },
                                error: function(data) {
                                    console.log('Error:', data);
                                    return false;

                                }
                            });
                        }
                    },
                    cancel: function() {
                        // Close
                    }
                }
            });
        }

        function handleTagClick(e) {
            if (!$(e.target).hasClass('tag-delete')) {
                const tagName = $(this).text().trim();
                window.location.href = window.location.pathname + '?tags[]=' + tagName;
            }
        }

        //<editor-fold defaultstate="collapsed" desc="Group channel">
        $("#group_channel_search").val({{ $request->c3 }});
        $('#group_channel_1').groupDropdown({
            inputSelector: '#group_channel_search',
            defaultSelected: Number($("#group_channel_search").val()),
            onSelect: function(selectedIds) {
                $("#group_channel_search").val(selectedIds[0]);
                $("#form-search").submit();
            }
        });
        $('#group_channel_2').groupDropdown({
            inputSelector: '#group_channel_search',
            defaultSelected: Number($("#group_channel_search").val()),
            onSelect: function(selectedIds) {
                $("#group_channel_search").val(selectedIds[0]);
                $("#form-search").submit();
            }
        });

        //</editor-fold>

        //<editor-fold defaultstate="collapsed" desc="action new">

        // Variables to track selected channels and actions
        let selectedChannels = [];
        let selectedAction = null;
        let selectedActionForm = null;
        let isPanelCollapsed = false;

        // Toggle floating action panel
        function toggleActionPanel() {
            if (selectedChannels.length > 0) {
                $('#actionPanel').addClass('show');
                $('.execute-btn').prop('disabled', selectedAction === null);
                $("#executeBtn").html('<i class="fas fa-play-circle"></i> Execute');
                $("#executeReloadBtn").html('<i class="fas fa-sync"></i> Execute & Reload');
            } else {
                $('#actionPanel').removeClass('show');
                $('.execute-btn').prop('disabled', true);
            }

            // Update selected count
            $('#selectedCount').text(selectedChannels.length);
        }

        // Initialize panel state
        toggleActionPanel();

        $('.checkbox-multi').change(function() {
            // Kiểm tra xem có checkbox nào được chọn hay không

            if ($('.checkbox-multi:checked').length > 0) {
                selectedChannels = $('.checkbox-multi:checked').map(function() {
                    return $(this).val();
                }).get();
            } else {
                selectedChannels = [];
            }
            toggleActionPanel();
        });

        // Theo dõi checkbox "Select All"
        $('#select_all').change(function() {
            $('.checkbox-multi').prop('checked', this.checked).trigger('change');
        });

        // Toggle panel collapse/expand
        $('#toggleActionPanelBtn').click(function(e) {
            e.preventDefault();
            if (isPanelCollapsed) {
                // Expand panel
                $('#actionPanel').css('right', '1.5rem');
                $(this).find('i').removeClass('fa-chevron-left').addClass('fa-chevron-right');
                isPanelCollapsed = false;
            } else {
                // Collapse panel
                $('#actionPanel').css('right', '-365px');
                $(this).find('i').removeClass('fa-chevron-right').addClass('fa-chevron-left');
                isPanelCollapsed = true;
            }
        });


        $('.action-btn').click(function(e) {
            e.preventDefault();
            const actionValue = $(this).data('value');
            const requiresForm = $(this).data('requires-form') === true;
            const formId = $(this).data('form');

            // Kiểm tra nếu button đang active, thì hủy active
            if ($(this).hasClass('btn-primary')) {
                // Hủy chọn action này
                $(this).removeClass('btn-primary').addClass('btn-outline-secondary').removeClass("active");
                selectedAction = null;

                // Ẩn form nếu đang hiển thị
                if (requiresForm && formId) {
                    $('#' + formId).removeClass('show');
                    selectedActionForm = null;
                }

                // Vô hiệu hóa nút Execute
                $('#executeBtn').prop('disabled', true);
                $('#executeReloadBtn').prop('disabled', true);

                // Reset hidden input
                $('#actionValue').val('');

                return; // Thoát khỏi hàm, không thực hiện các bước tiếp theo
            }

            // Nếu không phải là button đang active, tiến hành chọn nó

            // Deselect previously selected action
            $('.action-btn').removeClass('btn-primary').addClass('btn-outline-secondary').removeClass("active");

            // Hide all forms
            $('.action-form').removeClass('show');

            // Select this action
            $(this).removeClass('btn-outline-secondary').addClass('btn-primary').addClass("active");
            selectedAction = actionValue;

            // Update the hidden input
            $('#actionValue').val(actionValue);

            // Show form if required
            if (requiresForm && formId) {
                $('#' + formId).addClass('show');
                selectedActionForm = formId;
            } else {
                selectedActionForm = null;
            }

            // Enable execute button
            $('#executeBtn').prop('disabled', false);
            $('#executeReloadBtn').prop('disabled', false);
        });
        // Handle execute button click
        $('.execute-btn').click(function(e) {
            e.preventDefault();
            $('#executeBtn').prop('disabled', true);
            $('#executeReloadBtn').prop('disabled', true);
            const shouldReload = $(this).data('reload') === true;
            if (selectedChannels.length > 0 && selectedAction !== null) {
                let actionData = {
                    action: selectedAction,
                    channels: selectedChannels
                };
                var $this = $(this);
                var org = $this.html();
                $this.html(`<i class="fas fa-spinner fa-spin"></i> Loading...`);
                var formChannel = $("#formChannel").serialize();
                $.ajax({
                    type: "POST",
                    url: "/ajaxChannel",
                    data: formChannel,
                    dataType: 'json',
                    success: function(data) {
                        $this.html(org);
                        $('#executeBtn').prop('disabled', false);
                        $('#executeReloadBtn').prop('disabled', false);
                        for (var i = 0; i < data.content.length; i++) {
                            showNotification(data.content[i], data.status);
                            notify(data.content[i], "https://automusic.win/channelmanagement", "");
                        }
                        setTimeout(function() {
                            if (shouldReload) {
                                location.reload();
                            }
                        }, 1000);

                    },
                    error: function(data) {
                        $this.html($this.data('original-text'));
                    }
                });
            }
        });

        // Filter actions based on search input
        $('#actionSearch').on('input', function() {
            const searchTerm = $(this).val().toLowerCase();

            if (searchTerm === '') {
                // If search is empty, show all groups
                $('.action-group').show();
                $('.action-btn').show();
            } else {
                // First, hide all groups
                $('.action-group').hide();

                // Show buttons that match and their parent groups
                $('.action-btn').each(function() {
                    const buttonText = $(this).text().toLowerCase();
                    if (buttonText.includes(searchTerm)) {
                        $(this).show();
                        $(this).closest('.action-group').show();
                    } else {
                        $(this).hide();
                    }
                });
            }
        });


        $('#closeActionPanelBtn').click(function(e) {
            e.preventDefault();
            // Thêm class closing để tạo hiệu ứng
            $('#actionPanel').addClass('closing');

            // Sau khi animation hoàn tất, ẩn panel và bỏ chọn tất cả
            setTimeout(function() {
                // Bỏ chọn tất cả các channel
                $('.channel-select').prop('checked', false);
                $('.channel-row').removeClass('selected');

                // Reset các biến theo dõi
                selectedChannels = [];
                selectedAction = null;
                selectedActionForm = null;

                // Ẩn panel
                $('#actionPanel').removeClass('show closing');

                // Bỏ active của tất cả action buttons
                $('.action-btn').removeClass('btn-primary').addClass('btn-outline-secondary');

                // Ẩn tất cả form
                $('.action-form').removeClass('show');

                // Cập nhật số lượng đã chọn
                $('#selectedCount').text('0');
            }, 300);
        });

        //</editor-fold>

        //<editor-fold defaultstate="collapsed" desc="table">

        $(document).on('click', '.copyable-text', function() {
            const textToCopy = $(this).data('copy');
            copyToClipboard2(textToCopy);
            showCopiedEffect($(this));
        });

        // For channel name that copies ID
        $(document).on('click', '.copyable-channel', function() {
            const channelId = $(this).data('channel-id');
            copyToClipboard2(channelId);
            showCopiedEffect($(this));
        });



        function showChannelIdInput(id) {
            $.confirm({
                title: "Update Channel Id",
                content: '<input type="text" id="txt_channel_id" value="" class="form-control">',
                buttons: {
                    "Cancel": function() {},
                    "Update": {
                        btnClass: "btn-blue",
                        action: function() {
                            let channelId = this.$content.find("#txt_channel_id").val().trim();
                            $.ajax({
                                type: "GET",
                                url: "updateChannelId",
                                data: {
                                    "id": id,
                                    "channel_id": channelId
                                },
                                dataType: 'json',
                                success: function(data) {
                                    showNotification(data.message, data.status);
                                    if (data.status == "success") {
                                        $(`#btn-add-channel-id-${id}`).fadeOut();
                                        $(`#channel_name_${id}`).attr("data-channel-id",
                                            `https://www.youtube.com/channel/${channelId}`);
                                    }

                                },
                                error: function(data) {
                                    console.log('Error:', data);
                                }
                            });
                        }
                    }
                }
            });
        }

        function insertOtpKey(id) {
            console.log(id);
            $.confirm({
                title: "Add OTP Key",
                content: '<input type="text" id="txt_otp_key" value="" class="form-control">',
                buttons: {
                    "Cancel": function() {},
                    "Save": {
                        btnClass: "btn-blue",
                        action: function() {
                            let otpkey = this.$content.find("#txt_otp_key").val().trim();
                            $.ajax({
                                type: "GET",
                                url: "insertOtpKey",
                                data: {
                                    "id": id,
                                    "otpkey": otpkey
                                },
                                dataType: 'json',
                                success: function(data) {
                                    console.log(data);
                                    if (data.status == "error") {
                                        $.Notification.notify(data.status, 'top center', '', data
                                            .message);
                                    } else {
                                        $(`#btn-insert-otp-${id}`).fadeOut();
                                        copyToClipboard(data.data);
                                    }


                                },
                                error: function(data) {
                                    console.log('Error:', data);


                                }
                            });
                        }
                    }
                }
            });
        }

        function deleteChannel(id) {
            $.confirm({
                animation: 'rotateXR',
                title: "Confirm",
                content: "Are you sure you want to delete this channel?",

                buttons: {
                    confirm: {
                        text: 'Confirm',
                        btnClass: 'btn-red',
                        action: function() {
                            $.ajax({
                                url: "/updateChannelId",
                                method: "GET",
                                data: {
                                    id: id,
                                    delete: 1
                                },
                                success: function(response) {
                                    logger("Del:", response);
                                    $(`#channel-${id}`).hide();
                                },
                                error: function(xhr, status, error) {
                                    logger("error:", error);
                                }
                            });
                        }
                    },
                    cancel: function() {

                    }

                }
            });
        }

        //</editor-fold>



        //filter
        $('#showFilterBtn').click(function(e) {
            e.preventDefault();
            $('#filterPanel').toggleClass('show');
        });

        $('#closeFilterBtn').click(function(e) {
            e.preventDefault();
            $('#filterPanel').removeClass('show');
        });

        function autoSubmitSearch(event, input) {
            // Cập nhật giá trị vào ô search gốc để đồng bộ
            document.getElementById('search_channel').value = input.value;

            // Tự động submit sau khi người dùng ngừng gõ 500ms
//            clearTimeout(input.timer);
//            input.timer = setTimeout(() => {
//                $("#form-search").submit();
//            }, 500);

            // Nếu nhấn Enter thì submit ngay
            if (event.key === 'Enter') {
                clearTimeout(input.timer);
                $("#form-search").submit();
            }
        }

        // Đồng bộ giá trị khi người dùng nhập vào ô search gốc
        document.getElementById('search_channel').addEventListener('input', function() {
            document.getElementById('search_channel_header').value = this.value;
        });

        $("#user_search_header").change(function() {
            $("#user_search").val($(this).val()).selectpicker('refresh');
            $("#form-search").submit();
        });

        const $filterPanel = $('#filterPanel');
        const $showFilterBtn = $('#showFilterBtn');
        const $closeFilterBtn = $('#closeFilterBtn');
        const $formSearch = $('#form-search');

        // Function to check if a form control has a value
        function hasValue($element) {
            if (!$element.length) return false;

            const elementId = $element.attr('id');
            const elementValue = $element.val();

            // Đối với các phần tử select
            if ($element.is('select')) {
                // Với tất cả các select, không tính khi giá trị là -1 (tùy chọn "Select" mặc định)
                if (elementValue === '-1' || elementValue === 'Select' || elementValue === '') {
                    return false;
                }
                return true;
            }

            // Đối với checkbox và radio
            if ($element.is(':checkbox') || $element.is(':radio')) {
                return $element.is(':checked');
            }


            if ($element.prop('multiple')) {
                // Đảm bảo có các giá trị được chọn thực sự (không chỉ là một mảng trống)
                return elementValue && elementValue.length > 0;
            }

            // Đối với các input ẩn có ID cụ thể cần loại trừ khi có giá trị mặc định
            if ($element.attr('type') === 'hidden' && elementId === 'group_channel_search' && elementValue === '-1') {
                return false;
            }


            // Đối với text input, textarea, v.v.
            return elementValue && $.trim(elementValue) !== '';
        }

        // Function to count filled form controls
        function countFilledControls() {
            let count = 0;

            $formSearch.find('input, select, textarea').each(function() {
                const $element = $(this);
                const elementId = $element.attr('id');

                // Skip hidden inputs used for technical purposes
                if ($element.attr('type') === 'hidden' && $element.attr('name') === 'limit') {
                    return;
                }
                if ($element.attr('type') === 'hidden' && $element.attr('name') === 'is_change_info_error') {
                    return;
                }
                // Xử lý đặc biệt cho #tags - bỏ qua nếu không có tags được chọn
                if (elementId === 'tags') {
                    //                    const selectedTags = $element.val();
                    //                    if (!selectedTags || selectedTags.length === 0) {
                    //                        return; // Không đếm trường tags nếu không có tag nào được chọn
                    //                    }
                    return;
                }

                // Xử lý đặc biệt cho #group_channel_search và #search_channel, nếu 2 phần tử này thì không hiện ra
                if (elementId === 'group_channel_search' || elementId == 'search_channel' || elementId ==
                    'user_search') {
                    return;
                }
                if (hasValue($element)) {
                    logger("element", $element);
                    count++;
                }
            });

            return count;
        }

        // Function to update badge with count
        function updateFilterBadge() {
            const count = countFilledControls();
            // Remove existing badge if any
            $showFilterBtn.find('.filter-badge').remove();

            // Add badge if count > 0
            if (count > 0) {
                const $badge = $('<span class="filter-badge ">' + count + '</span>');
                $showFilterBtn.append($badge);

                // Show filter panel if there are filled filters
//                $filterPanel.addClass('show');
                //không hiện advance filter sau khi filter
                $filterPanel.removeClass('show');
            } else {
                // Hide filter panel by default if no filters are applied
                $filterPanel.removeClass('show');
            }
        }

        // Initialize on page load
        updateFilterBadge();

        $(".btn-add-channel").click(function(e) {
            e.preventDefault();
            $("#dialog_channel_add").modal("show");
        });
        $(".btn-create-channel").click(function(e) {
            e.preventDefault();
            $("#dialog_create_add").modal("show");
            $.ajax({
                type: "GET",
                url: "/genEmailInfo",
                data: {},
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $("#email_id").val("");
                    $("#automail_id").val("");
                    $("#fake_email").val(data.last_full_email);
                    $("#fake_name").val(data.name);
                    $("#fake_recovery").val(data.recovery);
                    $("#fake_phone").val(data.phone);
                    $("#fake_pass").val(data.pass);
                    $("#fake_birth").val(data.birthday);
                    $("#btn-create-email").html('<i class="fa fa-save"></i> Save');
                    $("#btn-open-brower").hide();
                    $("#btn-open-brower").attr("hash", "");
                },
                error: function(data) {
                    $(element).html($(element).data('original-text'));

                }
            });
        });
        $("#btn-open-brower").click(function() {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var hash = $(this).attr('hash');
            goLogin(hash);
            setTimeout(function() {
                $this.html($this.data('original-text'));
            }, 2000);
        });

        function createEmail(element) {
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(element).html() !== loadingText) {
                $(element).data('original-text', $(element).html());
                $(element).html(loadingText);
            }
            var form = $("#formCreateEmail").serialize();
            $.ajax({
                type: "GET",
                url: "/createEmail",
                data: form,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $(element).html($(element).data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                    if (data.status === "success") {
                        $(element).html('<i class="fa fa-edit"></i> Update');
                        $("#email_id").val(data.data.id);
                        $("#automail_id").val(data.data.api_job_id);
                        $("#btn-open-brower").show();
                        $("#btn-open-brower").attr("hash", data.data.hash_pass);
                    }
                },
                error: function(data) {
                    $(element).html($(element).data('original-text'));
                }
            });
        }

        function addChannel(element) {
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(element).html() !== loadingText) {
                $(element).data('original-text', $(element).html());
                $(element).html(loadingText);
            }
            var form = $("#formAddChannel").serialize();
            $.ajax({
                type: "GET",
                url: "/addChannel",
                data: form,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $(element).html($(element).data('original-text'));
                    $.Notification.autoHideNotify(data.status, 'top center', 'Notify', data.message);
                    if (data.status == "success") {
                        location.reload();
                    }
                },
                error: function(data) {
                    $(element).html($(element).data('original-text'));

                }
            });
        }

        $(".copy_profile_id").click(function() {
            var profileId = $(this).attr("data-profile");
            navigator.clipboard.writeText(profileId);
            $.Notification.notify("success", 'top center', '', 'Copied: ' + profileId);
        });

        $("#btnDesEdit").click(function() {
            $('#dialog_description_edit').modal({
                backdrop: true
            });
        });

        $(".view-realtime-chart").click(function() {
            $("#dialog_realtime_view_loading").show();
            $("#table-chart").html("");
            $("#chartHour-wrap").html("");
            $("#chartMinute-wrap").html("");
            var channel_id = $(this).attr("data-channel");
            var channel_name = $(this).attr("data-name");
            var $this = $(this);
            var loadingText = '<i class="fas fa-spinner fa-spin"></i> Chart';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            $.ajax({
                type: "GET",
                url: "getDataChart",
                data: {
                    "channel_id": channel_id
                },
                dataType: 'json',
                success: function(data) {
                    logger("getDataChart", data);
                    $this.html($this.data('original-text'));
                    if (data.status == "success") {
                        $("#dialog_realtime_view_loading").hide();
                        $("#chartHour-wrap").html('<canvas id="chartHour"></canvas>');
                        $("#chartMinute-wrap").html('<canvas id="chartMinute"></canvas>');
                        drawBarChart("chartHour", "Views · Last 48 hours", data.data48hour.label, data
                            .data48hour.value);
                        drawBarChart("chartMinute", "Views · Last 60 minutes", data.data60minutes.label,
                            data
                            .data60minutes.value);
                        $("#last-48-hour").html(number_format(data.data48hour.total48, 0, ',', '.'));
                        $("#last-60-minute").html(number_format(data.data60minutes.total60, 0, ',',
                            '.'));
                        var channelHeaderInfo = `<div class="d-flex align-items-center mb-2 mb-md-0">
                                    <img class="video-img_thumb" src="${data.channel_thumb}" 
                                         style="width: 50px; height: 50px; border-radius: 50%; margin-right: 15px; object-fit: cover;">
                                    <div>
                                        <h4 class="m-0 font-weight-bold">${channel_name}</h4>
                                        <span class="badge badge-primary" style="background: #6c5ce7;">Subscribers: ${number_format(data.subs, 0, ',', '.')}</span>
                                    </div>
                                </div>
                                <div class="d-flex flex-wrap stats-container">
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${data.level}</div>
                                        <div class="stat-label">Level</div>
                                    </div>
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${number_format(data.viewRate42, 0, ',', '.')} </div>
                                        <div class="stat-label">Rate 48h</div>
                                    </div>
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${number_format(data.viewRate6, 0, ',', '.')}</div>
                                        <div class="stat-label">Rate 6h</div>
                                    </div>
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${number_format(data.viewAvg, 0, ',', '.')}</div>
                                        <div class="stat-label">View Avg 6h</div>
                                    </div>
                                    <div class="stat-item text-center mx-3 my-1">
                                        <div class="stat-value">${data.last_sync}</div>
                                        <div class="stat-label">Last Sync</div>
                                    </div>
                                </div>
                                <div class="status-indicator ml-auto">
             
                                <button
                                    class="btn btn-circle btn-circle-cus btn-circle-hover waves-effect waves-light btn-close-modal"
                                    data-id="modal-add-task" data-toggle="tooltip" data-placement="top" title="Close" data-dismiss="modal"
                                    style="    padding: 0.5rem 0.7rem 0.5rem 0.7rem;z-index: 1001;border-radius: 50%;line-height: 1">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                                         class="bi bi-x-lg m-t-1" viewBox="0 0 16 16">
                                        <path
                                            d="M2.146 2.854a.5.5 0 1 1 .708-.708L8 7.293l5.146-5.147a.5.5 0 0 1 .708.708L8.707 8l5.147 5.146a.5.5 0 0 1-.708.708L8 8.707l-5.146 5.147a.5.5 0 0 1-.708-.708L7.293 8z" />
                                    </svg>
                                </button>
                                </div>`;
                        $("#channel-header-info").html(channelHeaderInfo);
                        $("#table-chart").html(
                            '<tr><th style="top:-20px">Content</th><th style="top:-20px">Published</th><th colspan="2" style="top:-20px;text-align: center">Last 48 hours</th><th colspan="2" style="top:-20px;text-align: center">Last 60 minutes</th></tr>'
                        );
                        var html = '';
                        $.each(data.topVideos, function(key, value) {
                            i = i + 1;
                            html =
                                '<tr><td><a target="_blank" href="https://www.youtube.com/watch?v=' +
                                value.video_id +
                                '"><img class="video-img_thumb" src="https://i.ytimg.com/vi/' +
                                value
                                .video_id + '/default.jpg">' + value.video_title +
                                '</a></td><td>' +
                                value.published + '</td><td><span>' + number_format(value
                                    .total_view_hour, 0, ',', '.') +
                                '</span></td><td><div><canvas id="chartHourMini' + key +
                                '"></canvas></div></td><td><span>' + number_format(value
                                    .total_view_minute, 0, ',', '.') +
                                '</span></td><td><div><canvas id="chartMinuteMini' + key +
                                '"></canvas></div></td></tr>';
                            $("#table-chart").append(html);
                            var ratio48 = value.total_view_hour / data.maxViewTopVideoHour *
                            100;
                            if (ratio48 < 25) {
                                ratio48 = 25;
                            }
                            drawBarChartMini("chartHourMini" + key, "48", value.times_hour,
                                value
                                .views_hour, ratio48);
                            ratio48 = value.total_view_minute / data.maxViewTopVideoMinute *
                            100;
                            if (ratio48 < 25) {
                                ratio48 = 25;
                            }
                            drawBarChartMini("chartMinuteMini" + key, "60", value.times_minute,
                                value
                                .views_minute, ratio48);
                        });
                        $('#dialog_realtime_view').modal({
                            backdrop: true
                        });

                    } else {
                        showNotification(data.message, data.status);
                    }

                },
                error: function(data) {
                    console.log('Error:', data);
                    $this.html($this.data('original-text'));
                }
            });
        });

        function viewChart(channel_id) {
            //        $("#content-dialog").html("");

        }
        let currentHighlightedButton = null;
        let buttonTimers = {};
        $('.btn-gologin').click(function(e) {
            e.preventDefault();
//            var id = $(this).val();
            var $this = $(this);
            var id = $this.val();
             // Kiểm tra nếu nút đang bị khóa
            if ($this.hasClass('locked')) {
                return; // Không làm gì nếu nút đang bị khóa
            }

            // Xóa highlight từ nút trước đó
            if (currentHighlightedButton && !currentHighlightedButton.is($this)) {
                currentHighlightedButton.removeClass('highlighted');
            }

            // Đặt nút hiện tại làm nút được highlight
            currentHighlightedButton = $this;

            // Thêm class highlighted và locked
            $this.addClass('highlighted locked');

            // Lưu trữ text gốc của nút
            var originalText = $this.html();
                    goLogin(id);
                     // Đặt timer để đếm ngược 10 giây
            var countdown = 10;

            // Cập nhật text của nút để hiển thị đếm ngược
            function updateButtonText() {
                if (countdown > 0) {
                    $this.html(`<i class="fas fa-hourglass-half"></i> ${countdown}s`);
                } else {
                    $this.html(originalText);
                    $this.removeClass('locked');
                    // Giữ trạng thái highlighted
                }
            }

            // Bắt đầu đếm ngược
            updateButtonText();

            var countdownInterval = setInterval(() => {
                countdown -= 1;

                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    $this.html(originalText);
                    $this.removeClass('locked');
                } else {
                    updateButtonText();
                }
            }, 1000);
        });

        function goLogin(id) {
            $.get('goLogin?hash=' + id, function(data) {
                logger('goLogin', data);
                //        $this.html($this.data('original-text'));
                if (data.gologin == null) {
                    $.Notification.notify("error", 'top center', '', "Got error, Gologin Id empty");
                } else {
                    navigator.clipboard.writeText(data.gologin);
                    const commitBtn = $(`#commit-${id}`);
                    const gmailValue = commitBtn.attr('data-mail');
                    const newHref = `AutoProfile://profile/commit/?id=${data.gologin}&gmail=${gmailValue}&force=1`;
                    commitBtn.attr('href', newHref);
                    window.open(
                        `AutoProfile://profile/login/?id=${data.gologin}&gmail=${data.note}&hash_id=${data.hash_pass}&startup_url=https://www.youtube.com/channel_switcher`,
                        "_blank");
                    //            location.reload();
                }
            });
        }

        $('.btn-copy-gologin').click(function(e) {
            e.preventDefault();
            var profile = $(this).attr("data-profile");
            navigator.clipboard.writeText(profile);
            $.Notification.notify("error", 'top center', '', "Copied " + profile);
        });

        $('.btn-getcode').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var id = $(this).val();
            $.get('getCodeLogin?hash=' + id, function(data) {
                $this.html($this.data('original-text'));
                if (data.status == "error") {
                    $.Notification.notify("error", 'top center', '', "Got error");
                } else {
                    //                $.Notification.notify("success", 'top center', '', data.data);
                    copyToClipboard(data.data);
                }

            });
        });

        $('.btn-getcode-recovery').click(function(e) {
            e.preventDefault();
            //            var $this = $(this);
            //            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            //            if ($(this).html() !== loadingText) {
            //                $this.data('original-text', $(this).html());
            //                $this.html(loadingText);
            //            }
            var id = $(this).val();
            $.get('getCodeRecovery?hash=' + id, function(data) {
                if (data.status == "error") {
                    $.Notification.notify("error", 'top center', '', "Got error");
                } else {
                    //                $.Notification.notify("success", 'top center', '', data.data);
                    copyToClipboard(data.data);
                }

            });
        });

        function randomAboutSection() {
            var genre = $("#channel_genre").val();
            if (genre === '-1') {
                $.Notification.notify('error', 'top center', '', "Please choose Genre first");
                return;
            }
            $.ajax({
                type: "GET",
                url: "randomAboutSection",
                data: {
                    "genre": genre
                },
                dataType: 'json',
                success: function(data) {
                    if (data.length > 0) {
                        $("#aboutSectionId").val(data[0].id);
                        $("#about_section").val(data[0].content);
                        $("#about_section_use").html(' (Used ' + data[0].count + ' times)');
                    }
                },
                error: function(data) {
                    console.log('Error:', data);
                }
            });
        }

        $("#brand_select").change(function() {
            var data = atob($(this).val()).split("@;@");
            $("#firstName").val(data[3]);
            $("#channel_genre").val(data[4]);
            $("#channel_subgenre").val(data[7].split(','));
            $("#brand_user").val(data[2]);
            $("#profile").val(data[5]);
            $("#banner").val(data[6]);
            $("#avatarView").attr("src", data[5]);
            var css = "url('" + data[6] + "')";
            $(".banner-view").css({
                "background-image": css
            });
        });

        $("#channel_genre").change(function() {

            //        $.ajax({
            //            type: "GET",
            //            url: "getSubgenre",
            //            data: {"genre": $(this).val()},
            //            dataType: 'text',
            //            success: function (data) {
            //                ("#channel_genre").html(data);
            //
            //            },
            //            error: function (data) {
            //                console.log('Error:', data);
            //
            //            }
            //        });
        });

        $(".btn-vip-render").click(function(e) {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }

            e.preventDefault();
            $.ajax({
                type: "GET",
                url: "vipRender",
                data: {
                    "channel_id": $this.val()
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);

                },
                error: function(data) {
                    console.log('Error:', data);
                    $.Notification.notify('error', 'top center', '', "Error");
                    $this.html($this.data('original-text'));

                }
            });
        });

        $(".btn-set-info").click(function(e) {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var form = $("#frmBrand");
            var formData = form.serialize();
            console.log(formData);
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "saveChannelInfo",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);

                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        });

        $(".btn-save-brand").click(function(e) {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var form = $("#frmBrand");
            var formData = form.serialize();
            console.log(formData);
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "saveBrandChannel",
                data: formData,
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $this.html($this.data('original-text'));
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    setTimeout(function() {
                        location.reload();
                    }, 2000);
                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        });

        $(".btn-submit-upload").click(function(e) {
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var type = $(this).val();
            var form_file = new FormData();
            var file = $("#" + type + "_upload").val();
            if (file != '') {
                console.log(file);
            } else {
                $.Notification.notify('error', 'top center', '', "You must choose a image");
                return;
            }

            form_file.append('image', $("#" + type + "_upload")[0].files[0]);
            form_file.append('_token', '{{ csrf_token() }}');
            e.preventDefault();
            $.ajax({
                type: "POST",
                url: "/image-upload",
                data: form_file,
                contentType: false,
                processData: false,
                success: function(data) {
                    $this.html($this.data('original-text'));
                    console.log(data);
                    if (data.status == 'success') {
                        var url = "http://automusic.win/" + data.uploaded_image;
                        $(".channel-name-view").html($("#firstName").val());
                        if (type == "banner") {
                            $("#banner").val(url);
                            var css = "url('http://automusic.win/" + data.uploaded_image + "')";
                            $(".banner-view").css({
                                "background-image": css
                            });
                        } else {
                            $("#profile").val(url);
                            $("#avatarView").attr("src", 'http://automusic.win/' + data.uploaded_image);
                        }

                    } else {
                        $.Notification.notify(data.status, 'top center', '', data.message[0]);
                    }
                },
                error: function(data) {
                    but.button('reset');
                }
            });
        });

        $(".btn-brand").click(function(e) {
            e.preventDefault();
            $("#idBrand").val($(this).val());
            $("#aboutSectionId").val('');
            $("#title-brand").html("Branding " + $(this).attr("channelName"));
            $(".modal-dialog").addClass("modal-80");
            $('#dialog_brand_channel').modal({
                backdrop: false
            });

            $.ajax({
                type: "GET",
                url: "loadChannelInfo",
                data: {
                    "id": $(this).val()
                },
                dataType: 'json',
                success: function(data) {
                    $("#brand_user").html(data.brandOwner);
                    $("#channel_type").html(data.channelType);
                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });


        });

        $('.btn-sync-athena').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var id = $(this).val();
            $.get('ajaxSyncAthena/' + id, function(data) {
                $this.html($this.data('original-text'));
                $.Notification.notify(data.status, 'top center', '', data.content);
                $("#btn-sync-athena" + id).html('<i class="fa fa-cloud-' + data.btnIcon + '"></i> ' + data
                    .btnText);
                $("#btn-sync-athena" + id).attr("data-original-title", data.btnTooltip);
                $("#btn-sync-athena" + id).removeClass("btn-danger");
                $("#btn-sync-athena" + id).removeClass("btn-success");
                $("#btn-sync-athena" + id).addClass("btn-" + data.btnColor);
                //        setTimeout(function() {
                //            location.reload();
                //        }, 2000);

            });
        });

        $('.btn-recheck-channel').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-spinner fa-spin"></i> Check';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var id = $(this).val();
            $.get('ajaxRecheckChannel/' + id, function(data) {
                $this.html($this.data('original-text'));
                showNotification(data.message, data.status);

                if (data.status === 'success' && data.data) {
                    // Sử dụng ID động để chọn TD cần cập nhật
                    var $growthViewsCell = $('#growth-views-' + id);
                    var $totalViewsCell = $('#total-views-' + id);

                    // Định dạng số views
                    var formattedTotalViews = number_format(data.data.views, 0, ',', '.');
                    var formattedGrowthViews = number_format(data.data.views_increase, 0, ',', '.');

                    // Xử lý thông tin tăng trưởng
                    var growthPercentage = data.data.growth_percentage + '%';
                    var growthArrow, growthClass;

                    if (data.data.growth_percentage >= 0) {
                        growthArrow = '<i class="fas fa-arrow-up growth-arrow text-success"></i>';
                        growthClass = 'text-success';
                    } else {
                        growthArrow = '<i class="fas fa-arrow-down growth-arrow text-danger"></i>';
                        growthClass = 'text-danger';
                    }

                    // Cập nhật ô growth views (lượt xem tăng)
                    $growthViewsCell.html(
                        '<span>' +
                        formattedGrowthViews + ' ' +
                        growthArrow + ' ' +
                        '<span class="growth-percentage ' + growthClass + '">' + growthPercentage +
                        '</span>' +
                        '</span>'
                    );

                    // Cập nhật ô tổng views và thời gian
                    $totalViewsCell.html(
                        '<div>' + formattedTotalViews + '</div>' +
                        '<div class="small text-muted">just now</div>'
                    );
                }
            });
        });

        $('.btn-sync-gologin').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var gologin = $this.attr("data-gologin");
            var gmail = $this.attr("data-gmail");
            $.Notification.confirm('warning', 'top center', 'Are you sure ?', 'boom');
            $(document).on('click', '.notifyjs-metro-base .boom_yes', function() {
                $(this).trigger('notify-hide');
                window.open("AutoProfile://profile/commit/?id=" + gologin + "&gmail=" + gmail + "&force=1",
                    "_blank");

            });
        });

        $('#btnExcute').click(function(e) {
            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            if ($("#action").val() == '0') {
                $this.html($this.data('original-text'));
                $.Notification.notify('error', 'top center', '', 'Choose one action');
                return;
            }
            var formChannel = $("#formChannel").serialize();
            $.ajax({
                type: "POST",
                url: "/ajaxChannel",
                data: formChannel,
                dataType: 'json',
                success: function(data) {
                    $this.html($this.data('original-text'));
                    for (var i = 0; i < data.content.length; i++) {
                        $.Notification.notify(data.status, 'top center', '', data.content[i]);
                        notify(data.content[i], "https://automusic.win/channelmanagement", "");

                    }
                    //                location.reload();
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });

        $('#btnCloseDialogGroupChannel').click(function() {
            $("#data-table-group-channel").DataTable().clear().destroy();
            $.ajax({
                type: "GET",
                url: '/ajaxGetGroupChannel',
                dataType: 'json',
                success: function(data) {
                    var option = '<option value="-1" ><?php echo trans('label.value.select'); ?></option>';
                    if (data !== null && data.length > 0) {
                        for (var i = 0; i < data.length; i++) {
                            option += '<option value="' + data[i].id + '">' + data[i].group_name +
                                '</option>';

                        }
                        $('#g_c_add').html(option);
                        $('#group_channel_search').html(option);
                    }
                },
                error: function(data) {}
            });

        });

        $("#btnCard").click(function(e) {
            e.preventDefault();
            var selected = [];
            var n = $("input:checked.checkbox-multi").length;
            //    $(' input:checked.checkbox-multi').each(function() {
            //        selected.push($(this).val());
            //    });
            if (n === 0) {
                $.Notification.notify("error", 'top center', '', "You have to choose channel");
                return;
            }
            $('#dialog_card_endscreen').modal({
                backdrop: true
            });

        });
        var id = 0;

        function addCard(type) {
            id = id + 1;
            $(".card-header").addClass("collapsed");
            $(".card-header").attr("aria-expanded", false);
            $(".collapse").removeClass("show");
            var html = '';
            //            html+='<div id="card" class="card">';
            html += `<div id="card${id}" class="card">`;
            html += '    <div class="card-header cur-poiter" role="tab" id="heading' + id +
                '" data-toggle="collapse" data-parent="#accordion" href="#collapse' + id +
                '" aria-expanded="true" aria-controls="collapse' + id + '">';
            html += '        <table class="w-100"><tr><td class="w-50"><h5 class="mb-0 mt-0 font-16"><a >' + type
                .capitalize() +
                ' Card</a></h5></td>';
            html +=
                `                <td class="w-30"><input name="${type}_time[]" type="text" data-mask="9:99:99" value="0:00:00" class="form-control input-sm pull-right radius-6 w-70px"></td><td><i class="ion-trash-b font-35 pull-right" onclick="deleteCard('card${id}')"></i></td></tr></table>`;
            html += '    </div>';
            html += '    <div id="collapse' + id + '" class="collapse show" role="tabpanel" aria-labelledby="heading' + id +
                '">';
            html += '        <div class="card-body">';
            html += '            <div class="row">';
            html += '                <div class="col-md-12">';
            html += '                    <div class="form-group row">';
            html += '                        <div class="col-12">';
            html +=
                `                            <input class="form-control radius-6" type="text"  name="${type}_link_card[]" placeholder="${type.capitalize()}">`;
            html += '                        </div>';
            html += '                    </div>';
            html += '                </div>';
            html += '                <div class="col-md-12">';
            html += '                    <div class="form-group row">';
            html += '                        <div class="col-12">';
            html +=
                `                            <textarea class="h-50px form-control line-heigh-125 radius-6" rows="5"  name="${type}_message_card[]" placeholder="Custom message 30 characters (${type == 'channel' ? 'require' : 'optional'})" spellcheck="false" maxlength="30"></textarea>`;
            html += '                        </div>';
            html += '                    </div>';
            html += '                </div>';
            html += '                <div class="col-md-12">';
            html += '                    <div class="form-group row">';
            html += '                        <div class="col-12">';
            html +=
                `                            <textarea class="h-50px form-control line-heigh-125 radius-6" rows="5"  name="${type}_intro_card[]" placeholder="Intro content 30 characters (${type == 'channel' ? 'require' : 'optional'})" spellcheck="false" maxlength="30"></textarea>`;
            html += '                        </div>';
            html += '                    </div>';
            html += '                </div>';
            html += '            </div>';
            html += '        </div>';
            html += '    </div>';
            html += '</div>';
            $("#accordion").append(html);
        }

        function deleteCard(id) {
            $("#" + id).remove();
        }

        $("#btnCardSubmit").click(function(e) {

            e.preventDefault();
            var $this = $(this);
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i> Loading...';
            if ($(this).html() !== loadingText) {
                $this.data('original-text', $(this).html());
                $this.html(loadingText);
            }
            var formChannel = $("#formChannel").serialize();
            $.ajax({
                type: "POST",
                url: "/addCardEndscreen",
                data: formChannel,
                dataType: 'json',
                success: function(data) {
                    $.Notification.notify(data.status, 'top center', '', data.message);
                    $this.html($this.data('original-text'));
                    console.log(data);
                },
                error: function(data) {
                    $this.html($this.data('original-text'));
                }
            });
        });

        Object.defineProperty(String.prototype, 'capitalize', {
            value: function() {
                return this.charAt(0).toUpperCase() + this.slice(1);
            },
            enumerable: false
        });

        $(".endsTemplate").click(function() {
            $(".endsTemplate").removeClass("endscreen-template-active");
            $(this).addClass("endscreen-template-active");
            var value = $(this).attr("data-tpl");
            $("#template_encscreen").val(value);
        });

        $("#btnCardManagement").click(function(e) {
            e.preventDefault();
            $('#dialog_card_management').modal({
                backdrop: true
            });
            $("#dialog_card_management_loading").fadeIn();
            $.ajax({
                type: "GET",
                url: "/getCardEndscreen",
                data: {},
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    $("#dialog_card_management_loading").fadeOut('fast');
                    var i = 0
                    if (data.length > 0) {
                        $.each(data, function(key, value) {
                            i = i + 1;
                            var type = 'Card';
                            if (value.type === 2) {
                                type = "Enscreen";
                            }
                            $("#tbl-card").append(
                                `<tr><td>${i}</td><td>${type}</td><td>${value.promo_link}</td><td>${value.total}</td></tr>`
                            );
                        });
                    }

                },
                error: function(data) {}
            });
        });

let channelsData = [];
let processedChannelsCount = 0; 
let successChannelsCount = 0;   
let totalChannelsCount = 0;
function chartTrackChannels() {
    processedChannelsCount = 0; 
    successChannelsCount = 0;   
    totalChannelsCount = 0;
    channelsData = [];
    
    let channelIds = [];
    try {
        const savedChannelIds = localStorage.getItem('trackChannelIds');
        if (savedChannelIds) {
            channelIds = JSON.parse(savedChannelIds);
            totalChannelsCount = channelIds.length;
        }
    } catch (error) {
        console.error("Error parsing channel IDs from localStorage:", error);
    }

    if (!channelIds.length) {
        showNotification("No channels selected for tracking", "error");
        return;
    }
    
    updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);

    $('#modal_multi_chart_realtime').modal({
        backdrop: true
    });

    $("#modal_multi_chart_realtime_loading").show();
    $("#charts-container").html("");

    var chartButton = $("#chartTrackButton");

    var loadingText = '<i class="fas fa-spinner fa-spin"></i> Chart';
    var originalText = chartButton.html();
    chartButton.html(loadingText);

    if (window.eventSource) {
        window.eventSource.close();
    }

    // Khởi tạo grid layout
    initializeGrid();
    
    // Khởi tạo mảng dữ liệu trước theo thứ tự của localStorage
    channelsData = new Array(channelIds.length);
    
    const idsParam = encodeURIComponent(JSON.stringify(channelIds));
    window.eventSource = new EventSource(`getDataCharts?ids=${idsParam}`);

    window.eventSource.onmessage = function(event) {
        try {
            const data = JSON.parse(event.data);
            logger("getDataCharts event", data);
            
            if (data.status == "success" || data.status == "error") {
                $("#modal_multi_chart_realtime_loading").hide();
                processedChannelsCount++;
                
                if (data.status == "success") {
                    successChannelsCount++;
                }
                
                const index = channelIds.indexOf(data.channel_id.toString());
                if (index !== -1) {
                    channelsData[index] = data;
                    addChannelToGrid(data, index);
                } else {
                    channelsData.push(data);
                    addChannelToGrid(data, channelsData.length - 1);
                }
                
                updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);
                
            } else if (data.status == "complete") {
                window.eventSource.close();
                chartButton.html(originalText);
                updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);
                updateGridLayout();
                
            } else {
                processedChannelsCount++;
                updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);
                showNotification(data.message || "Unknown status received", data.status || "warning");
            }
        } catch (error) {
            console.error("Error processing SSE event:", error);
        }
    };

    window.eventSource.onerror = function(error) {
        console.error("SSE Error:", error);
        window.eventSource.close();
        chartButton.html(originalText); 
        showNotification("Error receiving channel data", "error");
    };

    $('#modal_multi_chart_realtime').on('hidden.bs.modal', function() {
        if (window.eventSource) {
            window.eventSource.close();
        }
        updateChartButtonBadge();
    });
}

function initializeGrid() {
    const container = $("#charts-container");
    
    if (container.find('.sort-buttons').length === 0) {
        container.append(`
            <div class="sort-buttons">
                <button type="button" class="sort-btn" onclick="sortChannels('48h')">
                    <i class="fas fa-sort-amount-down"></i> Sort by 48h views
                </button>
                <button type="button" class="sort-btn" onclick="sortChannels('60m')">
                    <i class="fas fa-sort-amount-down"></i> Sort by 60m views
                </button>
            </div>
        `);
    }
}

function addChannelToGrid(data, index) {
    const container = $("#charts-container");
    const channel_id = data.channel_id;
    
    if ($(`.channel-col[data-channel-id="${channel_id}"]`).length > 0) {
        return;
    }
    
    const rowIndex = Math.floor(index / 2);
    const isFirstColumn = index % 2 === 0;
    
    let row = $(`#channel-row-${rowIndex}`);
    if (row.length === 0) {
        container.append(`<div id="channel-row-${rowIndex}" class="channel-row"></div>`);
        row = $(`#channel-row-${rowIndex}`);
    }
    
    const columnCount = row.children('.channel-col').length;
    
    if ((isFirstColumn && columnCount === 0) || (!isFirstColumn && columnCount === 1)) {
        row.append(createChannelColumn(data, index));
        setTimeout(() => {
            if (data.status === "success") {
                drawBarChart(`chartHour-${channel_id}`, `${data.channel_name} - Views · Last 48 hours`, 
                    data.data48hour.label, data.data48hour.value);
                drawBarChart(`chartMinute-${channel_id}`, `${data.channel_name} - Views · Last 60 minutes`, 
                    data.data60minutes.label, data.data60minutes.value);
            }
        }, 100);
    } else {
        updateGridLayout();
    }
}

function updateGridLayout() {
    const container = $("#charts-container");
    container.find('.channel-row').remove();
    const validChannelsData = channelsData.filter(item => item !== undefined);
    for (let i = 0; i < Math.ceil(validChannelsData.length / 2); i++) {
        container.append(`<div id="channel-row-${i}" class="channel-row"></div>`);
    }
    
    validChannelsData.forEach((data, index) => {
        const rowIndex = Math.floor(index / 2);
        const row = $(`#channel-row-${rowIndex}`);
        const channel_id = data.channel_id;
        let col = $(`.channel-col[data-channel-id="${channel_id}"]`);
        
        if (col.length > 0) {
            row.append(col.detach());
            col.attr('data-index', index);
        } else {
            row.append(createChannelColumn(data, index));
            setTimeout(() => {
                if (data.status === "success") {
                    drawBarChart(`chartHour-${channel_id}`, `${data.channel_name} - Views · Last 48 hours`, 
                        data.data48hour.label, data.data48hour.value);
                    drawBarChart(`chartMinute-${channel_id}`, `${data.channel_name} - Views · Last 60 minutes`, 
                        data.data60minutes.label, data.data60minutes.value);
                }
            }, 100);
        }
    });
    
    container.find('.channel-row').each(function() {
        if ($(this).children('.channel-col').length < 2) {
            $(this).append(`<div class="channel-col"></div>`);
        }
    });
}


function createChannelColumn(data, index) {
    const channel_id = data.channel_id;
    const channel_name = data.channel_name || "Unknown Channel";
    
    if (data.status === "success") {
        return `
            <div class="channel-col" data-channel-id="${channel_id}" data-index="${index}">
                <div class="channel-container">
                    <div class="channel-header p-3">
                        ${generateChannelInfoHtml(data)}
                        <div class="channel-actions">
                            <button type="button" class="action-btn move-up-btn" onclick="moveChannel(${index}, 'up')" title="Move Up">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button type="button" class="action-btn move-down-btn" onclick="moveChannel(${index}, 'down')" title="Move Down">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            <button type="button" class="action-btn remove-chart-btn" onclick="removeChannelFromCharts(${channel_id}, '${channel_name.replace(/'/g, "\\'")}')" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="chart-content p-3">
                        <div class="row">
                            <div class="col-md-6">
                                <div id="chartHour-wrap-${channel_id}" class="chart-container">
                                    <canvas id="chartHour-${channel_id}"></canvas>
                                </div>
                                <div class="text-center mt-2">
                                    <small>Views (Last 48 hours): <strong id="last-48-hour-${channel_id}">${number_format(data.data48hour.total48, 0, ',', '.')}</strong></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div id="chartMinute-wrap-${channel_id}" class="chart-container">
                                    <canvas id="chartMinute-${channel_id}"></canvas>
                                </div>
                                <div class="text-center mt-2">
                                    <small>Views (Last 60 minutes): <strong id="last-60-minute-${channel_id}">${number_format(data.data60minutes.total60, 0, ',', '.')}</strong></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else {
        return `
            <div class="channel-col" data-channel-id="${channel_id}" data-index="${index}">
                <div class="channel-container">
                    <div class="channel-header p-3">
                        ${generateChannelInfoHtml(data)}
                        <div class="channel-actions">
                            <button type="button" class="action-btn move-up-btn" onclick="moveChannel(${index}, 'up')" title="Move Up">
                                <i class="fas fa-arrow-up"></i>
                            </button>
                            <button type="button" class="action-btn move-down-btn" onclick="moveChannel(${index}, 'down')" title="Move Down">
                                <i class="fas fa-arrow-down"></i>
                            </button>
                            <button type="button" class="action-btn remove-chart-btn" onclick="removeChannelFromCharts(${channel_id}, '${channel_name.replace(/'/g, "\\'")}')" title="Remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="chart-content p-3">
                        ${generateErrorHtml(channel_id, channel_name, data.message || "Unknown error occurred")}
                    </div>
                </div>
            </div>
        `;
    }
}

function moveChannel(index, direction) {
    let newIndex;
    
    if (direction === 'up') {
        if (index === 0) return; 
        newIndex = index - 1;
    } else { 
        if (index === channelsData.length - 1) return; // Đã ở vị trí cuối cùng
        newIndex = index + 1;
    }
    const validChannelsData = channelsData.filter(item => item !== undefined);
    [validChannelsData[index], validChannelsData[newIndex]] = [validChannelsData[newIndex], validChannelsData[index]];
    channelsData = validChannelsData;
    updateLocalStorageOrder();
    updateGridLayout();
}

function updateLocalStorageOrder() {
    const channelIds = channelsData
        .filter(item => item !== undefined)
        .map(data => data.channel_id);
    localStorage.setItem('trackChannelIds', JSON.stringify(channelIds));
    updateChartButtonBadge();
}

function sortChannels(criterion) {
    // Đánh dấu nút sắp xếp đang active
    $(".sort-btn").removeClass("active");
    $(`.sort-btn:contains('${criterion === '48h' ? '48h' : '60m'}')`).addClass("active");
    
    // Lọc để loại bỏ các phần tử undefined
    let validData = channelsData.filter(item => item !== undefined);
    
    validData.sort((a, b) => {
        if (a.status !== "success" && b.status !== "success") return 0;
        if (a.status !== "success") return 1; // Đưa kênh lỗi xuống dưới
        if (b.status !== "success") return -1;
        
        if (criterion === '48h') {
            return b.data48hour.total48 - a.data48hour.total48; // Sắp xếp giảm dần theo 48h
        } else {
            return b.data60minutes.total60 - a.data60minutes.total60; // Sắp xếp giảm dần theo 60m
        }
    });
    
    // Gán lại mảng đã sắp xếp
    channelsData = validData;
    
    // Cập nhật localStorage với thứ tự mới
    updateLocalStorageOrder();
    
    // Vẽ lại grid
    updateGridLayout();
}

// Hàm tạo HTML cho phần hiển thị lỗi
function generateErrorHtml(channel_id, channel_name, errorMessage) {
    return `
        <div class="horizontal-error">
            <i class="fas fa-exclamation-circle text-danger error-icon"></i>
            <div class="error-info">
                <p class="mb-0">${errorMessage}</p>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="syncookie(${channel_id}, '${channel_name.replace(/'/g, "\\'")}')">
                Sync cookie
            </button>
        </div>
    `;
}

// Hàm tạo HTML cho phần channel-info
function generateChannelInfoHtml(data) {
    return `
        <div class="d-flex align-items-center">
            <div class="channel-avatar mr-3">
                <img src="${data.channel_thumb || 'path/to/default-avatar.png'}" alt="${data.channel_name}" style="width: 60px; height: 60px; border-radius: 50%; object-fit: cover;">
            </div>
            <div>
                <h6 class="mb-1">${data.channel_name}</h6>
                <div class="small text-muted">
                    <span class="mr-2"><i class="fas fa-users"></i> ${number_format(data.subs || 0, 0, ',', '.')}</span>
                    <span><i class="fas fa-eye"></i> ${number_format(data.views || 0, 0, ',', '.')}</span>
                </div>
            </div>
        </div>
    `;
}

function removeChannelFromCharts(channelId, channelName) {
    // Tìm vị trí của kênh trong mảng dữ liệu
    const index = channelsData.findIndex(data => data && data.channel_id === channelId);
    
    if (index !== -1) {
        // Xóa kênh khỏi mảng dữ liệu
        channelsData.splice(index, 1);
        
        // Giảm số lượng kênh đã xử lý
        successChannelsCount--;
        totalChannelsCount--;
        updateModalTitle(processedChannelsCount, successChannelsCount, totalChannelsCount);
        
        // Hủy chart để tránh memory leak
        const hourChart = document.getElementById(`chartHour-${channelId}`);
        const minuteChart = document.getElementById(`chartMinute-${channelId}`);
        
        if (hourChart && hourChart.__chart) {
            hourChart.__chart.destroy();
        }
        
        if (minuteChart && minuteChart.__chart) {
            minuteChart.__chart.destroy();
        }
        
        // Cập nhật localStorage
        updateLocalStorageOrder();
        
        // Cập nhật lại grid layout
        updateGridLayout();
        
        // Hiển thị thông báo
        showNotification(`Channel "${channelName}" removed from chart`, "info");
        
        // Nếu không còn kênh nào, hiển thị thông báo
        if (channelsData.filter(item => item !== undefined).length === 0) {
            $("#charts-container").html(`
                <div class="text-center p-5">
                    <div class="mb-3"><i class="fas fa-chart-bar fa-4x text-muted"></i></div>
                    <h4 class="text-muted">No channels to display</h4>
                    <p>All channels have been removed. Close this window and add channels to track.</p>
                </div>
            `);
        }
    }
}

function updateLocalStorageOrder() {
    const channelIds = channelsData
        .filter(item => item !== undefined)
        .map(data => data.channel_id);
    
    localStorage.setItem('trackChannelIds', JSON.stringify(channelIds));
    
    // Cập nhật badge trên nút Chart
    updateChartButtonBadge();
}

//        function generateChartHtml(channel_id, channel_name, data) {
//            return `
//                <div class="row">
//                    <div class="col-md-6">
//                        <div id="chartHour-wrap-${channel_id}" class="chart-container">
//                            <canvas id="chartHour-${channel_id}"></canvas>
//                        </div>
//                    </div>
//                    <div class="col-md-6">
//                        <div id="chartMinute-wrap-${channel_id}" class="chart-container">
//                            <canvas id="chartMinute-${channel_id}"></canvas>
//                        </div>
//                    </div>
//                </div>
//                <div class="row mt-2">
//                    <div class="col-md-6">
//                        <div class="text-center">
//                            <span>Views (Last 48 hours): </span>
//                            <strong id="last-48-hour-${channel_id}">${number_format(data.data48hour.total48, 0, ',', '.')}</strong>
//                        </div>
//                    </div>
//                    <div class="col-md-6">
//                        <div class="text-center">
//                            <span>Views (Last 60 minutes): </span>
//                            <strong id="last-60-minute-${channel_id}">${number_format(data.data60minutes.total60, 0, ',', '.')}</strong>
//                        </div>
//                    </div>
//                </div>
//            `;
//        }

        function updateModalTitle(processed, success, total) {
            const titleElement = $('#modal_multi_chart_realtime .modal-title');
            const isLoading = processed < total;
            const loadingIcon = isLoading ? '<i class="fas fa-spinner fa-spin ml-2"></i>' : '';
            titleElement.html(
                `<span class="mr-2">Channel Analytics</span> <span class="badge badge-info mb-0">${success}/${total} channels loaded ${loadingIcon}</span>`
                );
        }

        function updateAllTrackButtons() {
            let channelIds = getTrackedChannels();
            $('.track-channel-btn').each(function() {
                const channelId = parseInt($(this).data('channel-id'));
                const isTracked = channelIds.includes(channelId);

                updateSingleTrackButton($(this), isTracked);
            });
        }

        function updateSingleTrackButton(button, isTracked) {
            if (isTracked) {
                button.html(
                    '<i class="fas fa-times mr-2 text-danger"></i> <span class="text-danger">Remove Tracking</span>');
                button.attr('onclick', `removeChannelFromTracking(${button.data('channel-id')})`);
            } else {
                button.html('<i class="fas fa-plus mr-2"></i> Add Tracking');
                button.attr('onclick', `addChannelToTracking(${button.data('channel-id')})`);
            }
        }

        function getTrackedChannels() {
            try {
                const savedChannelIds = localStorage.getItem('trackChannelIds');
                return savedChannelIds ? JSON.parse(savedChannelIds) : [];
            } catch (error) {
                console.error("Error parsing tracked channels:", error);
                return [];
            }
        }

        function addChannelToTracking(channelId) {
            let channelIds = getTrackedChannels();
            channelId = parseInt(channelId);
            if (channelIds.length >= 10) {
                showNotification("You can track maximum 10 channels. Please remove some channels first.", "warning");
                return;
            }
            if (!channelIds.includes(channelId)) {
                channelIds.push(channelId);
                localStorage.setItem('trackChannelIds', JSON.stringify(channelIds));
                updateChartButtonBadge();
                const button = $(`.track-channel-btn[data-channel-id="${channelId}"]`);
                updateSingleTrackButton(button, true);

                showNotification("Channel added to tracking list", "success");
            }
        }

        function removeChannelFromTracking(channelId) {
            let channelIds = getTrackedChannels();
            channelId = parseInt(channelId);
            const initialLength = channelIds.length;
            channelIds = channelIds.filter(id => id !== channelId);

            if (channelIds.length < initialLength) {
                localStorage.setItem('trackChannelIds', JSON.stringify(channelIds));
                updateChartButtonBadge();
                const button = $(`.track-channel-btn[data-channel-id="${channelId}"]`);
                updateSingleTrackButton(button, false);
                showNotification("Channel removed from tracking list", "info");
            }
        }

        function updateChartButtonBadge() {
            let channelIds = getTrackedChannels();

            const channelCount = channelIds.length;
            const chartButton = $('#chartTrackButton');
            chartButton.find('.filter-badge').remove();
            if (channelCount > 0) {
                chartButton.html(
                    `<i class="fas fa-chart-line"></i> Chart <span style="background-color: #52bb56;" class="filter-badge">${channelCount}</span>`
                    );
            } else {
                chartButton.html(`<i class="fas fa-chart-line"></i> Chart`);
            }
        }
        
        function syncookie(channelId, channelName) {
            const syncButton = $(`#channel-container-${channelId} .btn-outline-primary`);
            const originalText = syncButton.html();
            syncButton.html('<i class="fas fa-spinner fa-spin"></i> Syncing...');
            syncButton.prop('disabled', true);
            $.ajax({
                url: '/ajaxChannel',
                type: 'POST',

                data: {
                    _token: '{{ csrf_token() }}',
                    action: 28,
                    chkChannelAll: [channelId]
                },
                success: function(response) {
                    syncButton.html(originalText);
                    syncButton.prop('disabled', false);
                    if(response.status=="success"){
                        showNotification("Send command sync cookie successfully. Please wait for the system to work", "success");
                    }
                },
                error: function(xhr) {
                    console.error('Error syncookie:', xhr.responseText);
                    showNotification("Failed to sync cookie", "error");
                    syncButton.html(originalText);
                    syncButton.prop('disabled', false);
                }
            });

        }
    
        $(document).ready(function() {
            updateAllTrackButtons();
            updateChartButtonBadge();
        });
    </script>
@endsection
