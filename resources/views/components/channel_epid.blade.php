@extends('layouts.master')

@section('content')
    <style>
/*        .select-dropdown-container {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .select-dropdown-menu {
            display: none;
            position: absolute;
            width: 550px;
            max-height: 550px;
            overflow-y: auto;
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
            color: #17a2b8;
            cursor: pointer;
        }

        .edit-btn:hover,
        .delete-btn:hover,
        .go-top-btn:hover {
            color: #000;
        }*/
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

        .content-page > .content{
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
            font-size: 12px;
            position: absolute;
            top: -8px;
            right: -8px;
            font-weight: bold;
        }
        
        .gmail-count{
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
        .epid_pending {
            background-color: #2431e7;
        }
        .epid_approved {
            background-color: #28a745;
        }
        .epid_sent_epid {
            background-color: #fd7e14;
        }
        .epid_rejected {
            background-color: #dc3545;
        }
        .epid_off {
            background-color: #6c757d;
        }
        
        

        .status-icon {
            width: 16px;
            height: 16px;
            border-radius: 50%;
            margin-right: 10px;
            display: inline-block;
        }
    </style>

        <form id="form-search" action="/channel/epid">
            <input type="hidden" name="limit" id="limit" value="{{ $limit }}">
            {{ csrf_field() }}

        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center ">
                    <!--<h5 class="text-dark  header-title mb-0">CHANNEL LIST ({{ $datas->total() }})</h5>-->
                    <div class="row w-100">
                        <div class="header-search-container col-md-2">
                            <div class="input-group">
                                <input type="text" class="form-control search-input" id="search_channel_header" name='c1' value='{{$request->c1}}'
                                    value="{{ $request->c1 }}" placeholder="Search ID, name or email..."
                                    onkeyup="autoSubmitSearch(event, this)">
                                <div class="search-icon">
                                    <i class="fas fa-search text-muted"></i>
                                </div>
                            </div>
                        </div>
                        @if ($is_admin_music)
                            <div class="col-md-3 btn-100">
                                <div class="col-12">
                                    <select id="user_search_header" class="form-control search_select" name="c5"
                                        data-show-subtext="true" data-live-search="true" data-size="5"
                                        data-container="body" data-width="100%">
                                        {!! $listusercode !!}
                                    </select>
                                </div>
                            </div>
                        @endif
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
                                <th class="th-channel" colspan="4">@sortablelink('chanel_name', "Channel Details ($count)")</th>
                                <th class="th-increase">@sortablelink('increasing', 'Growth')</th>
                                <th class="th-views">@sortablelink('view_count', 'Views')</th>
                                <th class="text-center">Subs</th>
                                <th class="text-center">Start View</th>
                                <!--<th class="text-center">Start Subs</th>-->
                                <th class="text-center">Growth View</th>
                                <!--<th class="text-center">Growth Subs</th>-->
                                <th class="text-center">Status</th>
                                <th class="th-start">Action Date</th>
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
                                    <td colspan="4">
                                        <div class="d-flex align-items-center">
                                            <div class="channel-avatar-v1 position-relative">
                                                <img data-id="{{ $data->id }}" src="{{ $data->channel_clickup }}"
                                                    class="rounded-circle" alt="Channel Avatar"
                                                    onerror="this.src='/images/default-avatar.png'">

                                                <!-- Edit button overlay -->
                                                <button type="button" class="avatar-sync-btn cur-poiter"
                                                    value="{{ $data->id }}">
                                                   <i class="fas fa-sync-alt"></i>
                                                </button>
   
                                            </div>

                                            <div class="channel-info">
                                                <div class="channel-name-container">
                                                    <h5 class="mb-0 d-flex align-items-center">
                                                        <span id="channel_name_{{ $data->id }}"
                                                            class="channel-name copyable-channel <?php echo $data->status == 0 ? 'song-block' : ''; ?>"
                                                            data-channel-id="https://www.youtube.com/channel/{{ $data->chanel_id }}">{{ $data->chanel_name }}</span>
                                                        <!--                                                        <i class="fas fa-check-circle text-success ml-1"
                                                                            data-toggle="tooltip" title="Epid channel"></i>-->
                                                    </h5>
                                                </div>
                                                <div class="channel-details mt-2">
                                                    <div class="detail-row d-flex text-muted">
                                                        <div class="detail-item mr-3">
                                                            <i class="fas fa-hashtag fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->id }}">{{ $data->id }}</span>
                                                        </div>
                                                        <div class="detail-item mr-3">
                                                            <i class="fas fa-user fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ substr($data->user_name, 0, strripos($data->user_name, '_')) }}">{{ substr($data->user_name, 0, strripos($data->user_name, '_')) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="detail-row d-flex text-muted">
                                                        <div class="detail-item mr-3">
                                                            <i class="fas fa-at fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->handle }}">{{ $data->handle }}</span>
                                                        </div>
              
<!--                                                        <div class="detail-item">
                                                            <i class="fas fa-envelope fa-fw text-muted mr-2"></i> <span
                                                                class="copyable-text"
                                                                data-copy="{{ $data->note }}">{{ $data->note }}</span> 
                                                        </div>-->
                                                    </div>
                                                    <div class="detail-row d-flex {{$data->reward_color}}">
                                                        <div class="detail-item mr-3">
                                                            <i class="fas fa-award fa-fw mr-2"></i> <span
                                                                class="">{{ $data->reward_name }}</span>
                                                        </div>                                                        
                                                        <div class="detail-item mr-3">
                                                            <!--<i class="fas fa-money fa-fw text-muted mr-2"></i>-->
                                                            <i class="fas fa-money-bill-wave mr-2"></i>
                                                            <span>{{number_format($data->reward_money, 0, ',', '.')}}</span>
                                                        </div>
                                                        <div class="detail-item">
                                                            <i class="fas fa-moon-o fa-fw  mr-2"></i> 
                                                            <span>{{$data->reward_mooncoin}}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="action-buttons mt-3">

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
                                                    @if($request->is_admin_music)        
                                                    <div class="dropdown d-inline-block">
                                                        <button type="button"
                                                            class="btn btn-sm btn-action dropdown-toggle" type="button"
                                                            id="channelActions230430" data-toggle="dropdown">
                                                            <i class="fas fa-ellipsis-h"></i> More
                                                        </button>
                                                        <div class="dropdown-menu dropdown-menu-right">
                                                            @if($data->epid_status!=\App\Http\Models\AccountInfo::STATUS_SENT_EPID)
                                                            <button type="button" class="dropdown-item cur-poiter d-flex align-items-center" 
                                                                    onclick="submitEpid({{$data->id}},'{{\App\Http\Models\AccountInfo::STATUS_SENT_EPID}}')">
                                                                <span class="status-icon epid_sent_epid"></span> <span>Sent To Epid</span>
                                                            </button>
                                                            @endif
                                                            @if($data->epid_status!=\App\Http\Models\AccountInfo::STATUS_REJECTED)
                                                            <button type="button" class="dropdown-item cur-poiter d-flex align-items-center" 
                                                                    onclick="submitEpid({{$data->id}},'{{\App\Http\Models\AccountInfo::STATUS_REJECTED}}')">
                                                                <span class="status-icon epid_rejected"></span> <span>Reject</span>
                                                            </button>
                                                            @endif
                                                            @if($data->epid_status!=\App\Http\Models\AccountInfo::STATUS_EPID_APPROVED)
                                                            <button type="button" class="dropdown-item cur-poiter d-flex align-items-center" 
                                                                    onclick="submitEpid({{$data->id}},'{{\App\Http\Models\AccountInfo::STATUS_EPID_APPROVED}}')">
                                                                <span class="status-icon epid_approved"></span> <span>Approve</span>
                                                            </button>
                                                            @endif
                                                            <div class="dropdown-divider"></div>
                                                            @if($data->epid_status!=\App\Http\Models\AccountInfo::STATUS_EPID_OFF)
                                                            <button  type="button" class="dropdown-item cur-poiter d-flex align-items-center"
                                                                onclick="deleteChannel({{ $data->id }},'{{\App\Http\Models\AccountInfo::STATUS_EPID_OFF}}')">
                                                                <span class="status-icon epid_off"></span> <span>Remove Epid</span>
                                                            </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    @endif


                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td id="growth-views-{{$data->id}}">
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
                                    <td id="total-views-{{$data->id}}">
                                        <div>{{ number_format($data->view_count, 0, ',', '.') }}</div>
                                        <div class="small text-muted">{{ $data->inc_time }}</div>
                                    </td>
                                    <td class="text-center">{{ number_format($data->subscriber_count, 0, ',', '.') }}</td>
                                    
                                    @if($data->epid_status=='approved')
                                        <td class="text-center">{{ number_format($data->view_approved, 0, ',', '.')}}</td>
                                        <!--<td class="text-center">{{ number_format($data->sub_approved, 0, ',', '.')}}</td>-->
                                        <td class="text-center">{{ number_format($data->view_count - $data->view_approved, 0, ',', '.')}}</td>
                                        <!--<td class="text-center">{{ number_format($data->subscriber_count - $data->sub_approved, 0, ',', '.')}}</td>-->
                                    @else
                                        <td class="text-center">{{number_format($data->view_pending, 0, ',', '.')}}</td>
                                        <!--<td class="text-center">{{ number_format($data->sub_pending, 0, ',', '.')}}</td>-->
                                        <td class="text-center">{{ number_format($data->view_count - $data->view_pending, 0, ',', '.')}}</td>
                                        <!--<td class="text-center">{{ number_format($data->subscriber_count - $data->sub_pending, 0, ',', '.')}}</td>-->
                                    @endif

                                    <td>
                                        <span class="badge epid_{{$data->epid_status}} d-block mb-1 cur-poiter position-relative">
                                            {{$data->getEpidStatusText()}}
                                         </span>
                                    </td>                                    
                                    <td>
                                        @if ($data->epid_time != null)
                                        <span data-toggle="tooltip" data-placement="top" data-original-title="{{ App\Common\Utils::timeToStringGmT7($data->epid_time) }}">{{ App\Common\Utils::calcTimeText($data->epid_time) }}</span>
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

    </form>

    @include('dialog.groupchannel')
    @include('dialog.realtimeviews')


@endsection

@section('script')
    <script type="text/javascript">

        $(".avatar-sync-btn").click(function(){            
            const button = $(this);
            const avatarContainer = button.closest('.channel-avatar-v1');
            const img = avatarContainer.find('img');
            const channelRow = avatarContainer.closest('tr.channel-row');
            const id = channelRow.data('id');
            button.css({"opacity":"1"});
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
                        button.css({"opacity":"0"});
                        button.html('<i class="fas fa-sync-alt"></i>');
                        button.prop('disabled', false);
                        if (data.status == "success") {
                            img.attr("src",data.thumb);                    
//                            button.remove();
                        } 


                    },
                    error: function(data) {
                        console.log('Error:', data);
                        button.html('<i class="fas fa-sync-alt"></i>');
                        button.prop('disabled', false);

                    }
                });
            
        });

        function submitEpid(id,status){
            $.ajax({
                type: "GET",
                url: "/channel/epid/status",
                data: {
                    "id": id,
                    "status":status
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    showNotification(data.message, data.status);
                    if (data.status == "success") {

                    } 


                },
                error: function(data) {
                    console.log('Error:', data);

                }
            });
        }

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


        // Function to show notification
        function showNotification(message, type = 'info') {
            // Create notification element if it doesn't exist
            if ($('#notification-container').length === 0) {
                $('body').append(
                    '<div id="notification-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>'
                );
            }

            // Generate a unique ID for this notification
            const id = 'notification-' + Date.now();

            // Create the notification HTML
            let bgColor = '';
            let icon = '';

            switch (type) {
                case 'success':
                    bgColor = 'var(--success)';
                    icon = '<i class="fas fa-check-circle mr-2"></i>';
                    break;
                case 'error':
                    bgColor = 'var(--danger)';
                    icon = '<i class="fas fa-exclamation-circle mr-2"></i>';
                    break;
                case 'warning':
                    bgColor = 'var(--warning)';
                    icon = '<i class="fas fa-exclamation-triangle mr-2"></i>';
                    break;
                case 'info':
                default:
                    bgColor = 'var(--info)';
                    icon = '<i class="fas fa-info-circle mr-2"></i>';
                    break;
            }

            const notificationHTML = `
            <div id="${id}" class="notification" style="
                background-color: ${bgColor};
                color: white;
                padding: 15px 20px;
                border-radius: 4px;
                margin-bottom: 10px;
                box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                display: flex;
                align-items: center;
                justify-content: space-between;
                min-width: 300px;
                opacity: 0;
                transform: translateX(100px);
                transition: all 0.3s ease;
            ">
                <div>${icon}${message}</div>
                <button class="close-btn" style="
                    background: none;
                    border: none;
                    color: white;
                    font-size: 16px;
                    cursor: pointer;
                    margin-left: 10px;
                    padding: 0;
                ">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

            // Add the notification to the container
            $('#notification-container').append(notificationHTML);

            // Show the notification with animation
            setTimeout(() => {
                $(`#${id}`).css({
                    'opacity': '1',
                    'transform': 'translateX(0)'
                });
            }, 10);

            // Set up the close button
            $(`#${id} .close-btn`).click(function() {
                removeNotification(id);
            });

            // Auto-remove after 5 seconds
            setTimeout(() => {
                removeNotification(id);
            }, 5000);
        }

        // Function to remove a notification with animation
        function removeNotification(id) {
            $(`#${id}`).css({
                'opacity': '0',
                'transform': 'translateX(100px)'
            });

            setTimeout(() => {
                $(`#${id}`).remove();
            }, 300);
        }



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

        // Helper function to copy text to clipboard
        function copyToClipboard2(text) {
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
        }

        // Helper function to show the copied effect
        function showCopiedEffect(element) {
            element.addClass('copied');
            setTimeout(() => {
                element.removeClass('copied');
            }, 2000);
        }

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

            // Tự động submit sau khi người dùng ngừng gõ 500ms
            clearTimeout(input.timer);
            input.timer = setTimeout(() => {
                $("#form-search").submit();
            }, 500);

            // Nếu nhấn Enter thì submit ngay
            if (event.key === 'Enter') {
                clearTimeout(input.timer);
                $("#form-search").submit();
            }
        }

        $("#user_search_header").change(function() {
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
                // Xử lý đặc biệt cho #tags - bỏ qua nếu không có tags được chọn
                if (elementId === 'tags') {
//                    const selectedTags = $element.val();
//                    if (!selectedTags || selectedTags.length === 0) {
//                        return; // Không đếm trường tags nếu không có tag nào được chọn
//                    }
                    return;
                }

                // Xử lý đặc biệt cho #group_channel_search và #search_channel, nếu 2 phần tử này thì không hiện ra
                if (elementId === 'group_channel_search' || elementId=='search_channel') {
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
                $filterPanel.addClass('show');
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
            var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
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
                    //                console.log(data);
                    $this.html($this.data('original-text'));
                    $("#dialog_realtime_view_loading").hide();
                    $("#chartHour-wrap").html('<canvas id="chartHour"></canvas>');
                    $("#chartMinute-wrap").html('<canvas id="chartMinute"></canvas>');
                    drawBarChart("chartHour", "Views · Last 48 hours", data.data48hour.label, data
                        .data48hour.value);
                    drawBarChart("chartMinute", "Views · Last 60 minutes", data.data60minutes.label,
                        data
                        .data60minutes.value);
                    $("#last-48-hour").html(number_format(data.data48hour.total48, 0, ',', '.'));
                    $("#last-60-minute").html(number_format(data.data60minutes.total60, 0, ',', '.'));
                    $("#tbl-channel-info").html('<tr><td><img class="video-img_thumb" src="' + data
                        .channel_thumb + '">' + channel_name + '</td><td>Subs: ' + data.subs +
                        '</td><td>Level: ' + data.level + '</td><td>Rate 48h: ' + data.viewRate42 +
                        '</td><td>Rate 6h: ' + data.viewRate6 + '</td><td>View Avg 6h: ' + data
                        .viewAvg + '</td><td>Last Sync: ' + data.last_sync +
                        '</td><td><i class="fa color-green fa-circle heart"></i> Updating</td></tr>'
                    );
                    $("#table-chart").html(
                        '<tr><th>Content</th><th>Published</th><th colspan="2" style="text-align: center">Last 48 hours</th><th colspan="2" style="text-align: center">Last 60 minutes</th></tr>'
                    );
                    var html = '';
                    $.each(data.topVideos, function(key, value) {
                        i = i + 1;
                        html =
                            '<tr><td><a target="_blank" href="https://www.youtube.com/watch?v=' +
                            value.video_id +
                            '"><img class="video-img_thumb" src="https://i.ytimg.com/vi/' +
                            value
                            .video_id + '/default.jpg">' + value.video_title + '</a></td><td>' +
                            value.published + '</td><td><span>' + number_format(value
                                .total_view_hour, 0, ',', '.') +
                            '</span></td><td><div><canvas id="chartHourMini' + key +
                            '"></canvas></div></td><td><span>' + number_format(value
                                .total_view_minute, 0, ',', '.') +
                            '</span></td><td><div><canvas id="chartMinuteMini' + key +
                            '"></canvas></div></td></tr>';
                        $("#table-chart").append(html);
                        var ratio48 = value.total_view_hour / data.maxViewTopVideoHour * 100;
                        if (ratio48 < 25) {
                            ratio48 = 25;
                        }
                        drawBarChartMini("chartHourMini" + key, "48", value.times_hour, value
                            .views_hour, ratio48);
                        ratio48 = value.total_view_minute / data.maxViewTopVideoMinute * 100;
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

        $('.btn-gologin').click(function(e) {
            e.preventDefault();
            //    var $this = $(this);
            //    var loadingText = '<i class="fa fa-circle-o-notch fa-spin"></i>';
            //    if ($(this).html() !== loadingText) {
            //        $this.data('original-text', $(this).html());
            //        $this.html(loadingText);
            //    }
            var id = $(this).val();
            goLogin(id);
        });

        function goLogin(id) {
            $.get('goLogin?hash=' + id, function(data) {
                logger('goLogin', data);
                //        $this.html($this.data('original-text'));
                if (data.gologin == null) {
                    $.Notification.notify("error", 'top center', '', "Got error, Gologin Id null");
                } else {
                    navigator.clipboard.writeText(data.gologin);
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
                $this.html($this.data('original-text'));
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
                        formattedGrowthViews +' '+
                        growthArrow +' '+
                        '<span class="growth-percentage ' + growthClass + '">' + growthPercentage + '</span>' +
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
    </script>
@endsection
