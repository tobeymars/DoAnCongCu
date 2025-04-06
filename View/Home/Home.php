    <?php include '../shares/header.php'; ?>
    <?php
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../Model/VenuesModel.php';
    require_once __DIR__ . '/../../Model/EventModel.php';
    $database = new Database();
    $conn = $database->getConnection();
    $Venuemodel = new Venue($conn);
    $Eventmodel = new Event($conn);
    $venues = $Venuemodel->getAllVenuesHome()->fetchAll(PDO::FETCH_ASSOC);
    $events = $Eventmodel->getActiveEvents()->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
        <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
        <title>Quản Lý Sự Kiện</title>
        <link rel="stylesheet" href="../Home/style.css">
    </head>
    <style>
        .intro-section {
            width: 100%;
            text-align: center;
            padding-top: 50px;
            padding-bottom: 30px;
        }

        .section-title {
            font-size: 28px;
            margin-bottom: 40px;
            color: #333;
            position: relative;
            max-width: 1200px;
            margin-left: auto;
            margin-right: auto;
            padding: 0 20px;
        }

        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background-color: #ff6b6b;
        }

        .message-container {
            position: relative;
            height: 500px;
            width: 100%;
            margin: 0;
        }

        .message-box {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 500px;
            opacity: 0;
            transition: opacity 0.8s ease-in-out;
            box-sizing: border-box;
            display: none;
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            opacity: 0;
            transform: scale(0.95);
            transition: opacity 0.8s ease, transform 0.8s ease;
            overflow: hidden;
        }

        .message-box.active {
            opacity: 1;
            display: block;
        }

        .box1 {
            background-image: url('https://bazaarvietnam.vn/wp-content/uploads/2024/04/anh-trai-say-hi-thum.jpg');
        }

        .box2 {
            background-image: url('https://nads.1cdn.vn/2023/05/17/foodtography-63_1.jpg');
        }

        .box3 {
            background-image: url('https://i.ytimg.com/vi/2XSwCEj4mzw/maxresdefault.jpg');
        }

        .box4 {
            background-image: url('http://www.designstudies.vn/media/images/facebook_banner___talkshow.2e16d0ba.fill-800x450.jpg');
        }

        .box-content {
            position: relative;
            z-index: 2;
            padding: 0;
            color: white;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: rgba(0, 0, 0, 0.6);
        }

        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 40px;
        }

        .message-box h3 {
            margin-top: 0;
            color: white;
            font-size: 28px;
            margin-bottom: 15px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .message-box p {
            font-size: 18px;
            line-height: 1.6;
            color: white;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
            margin: 0;
        }

        .navigation {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            gap: 12px;
            position: relative;
            z-index: 10;
        }

        .dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background-color: #ddd;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .dot.active {
            background-color: #ff6b6b;
            transform: scale(1.2);
        }

        @media (max-width: 768px) {
            .message-box h3 {
                font-size: 24px;
            }

            .message-box p {
                font-size: 16px;
            }

            .content-wrapper {
                padding: 0 20px;
            }
        }

        .about-section {
            padding: 40px;
            background-color: #fff;
            font-family: 'Segoe UI', sans-serif;
        }

        .about-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }

        .about-image {
            flex: 1 1 45%;
            padding: 10px;
        }

        .about-image img {
            width: 100%;
            height: auto;
            border-radius: 8px;
            object-fit: cover;
        }

        .about-content {
            flex: 1 1 50%;
            padding: 10px 30px;
            color: #333;
        }

        .about-content h4 {
            font-size: 18px;
            color: #001854;
            margin-bottom: 10px;
            text-transform: uppercase;
        }

        .about-content h2 {
            font-size: 36px;
            color: #d4af37;
            /* màu vàng gold */
            font-weight: bold;
            margin-bottom: 20px;
            text-shadow: 1px 1px 1px rgba(0, 0, 0, 0.1);
        }

        .about-content p {
            font-size: 16px;
            line-height: 1.8;
            margin-bottom: 10px;
        }

        .fade-left,
        .fade-up {
            opacity: 0;
            transform: translateY(50px);
            transition: all 1s ease;
        }

        .fade-left {
            transform: translateX(-100px);
        }

        .fade-up {
            transform: translateY(100px);
        }

        .fade-in {
            opacity: 1 !important;
            transform: translateX(0) translateY(0) !important;
        }

        h2.title {
            text-align: center;
            color: #c39c49;
            font-size: 32px;
            font-weight: bold;
            position: relative;
            margin-bottom: 40px;
        }

        h2.title::after {
            content: "";
            display: block;
            width: 100px;
            height: 3px;
            background: #2470dc;
            margin: 10px auto 0;
            border-radius: 5px;
        }

        .services {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 20px;
            max-width: 1200px;
            margin: auto;
        }

        .service-card {
            flex: 1 1 calc(33% - 20px);
            border: 2px solid #007aff40;
            border-radius: 10px;
            overflow: hidden;
            background: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            text-align: center;
            transform: translateY(30px);
            opacity: 0;
            transition: all 0.8s ease;
        }

        .service-card img {
            width: 100%;
            height: 200px;
            display: block;
            transform: translateX(-30px);
            opacity: 0;
            transition: all 0.8s ease;
        }

        .service-card h3 {
            font-size: 18px;
            font-weight: bold;
            color: #001f54;
            margin: 15px 0 20px;
            transform: translateY(20px);
            opacity: 0;
            transition: all 0.8s ease;
        }

        .service-card.fade-in img,
        .service-card.fade-in h3 {
            transform: translate(0, 0);
            opacity: 1;
        }

        .service-card.fade-in {
            transform: translateY(0);
            opacity: 1;
        }

        @media (max-width: 768px) {
            .service-card {
                flex: 1 1 100%;
            }
        }
    </style>

    <body>
        <div class="hero-header">
            <h1 class="display-4 text-center">🎆 Mỗi sự kiện là một trải nghiệm – <strong>Khám phá ngay!</strong></h1>
        </div>
        <section class="intro-section">
            <h2 class="section-title fade-up">Chúng tôi mang đến những giá trị khác biệt</h2>
            <div class="message-container fade-left">
                <div class="message-box box1">
                    <div class="box-content">
                        <h3>🎨 Thiết kế ấn tượng</h3>
                        <p>Không gian sự kiện được thiết kế độc đáo, tạo dấu ấn mạnh mẽ cho người tham dự.</p>
                    </div>
                </div>
                <div class="message-box box2">
                    <div class="box-content">
                        <h3>💡 Ý tưởng độc đáo</h3>
                        <p>Mỗi sự kiện là một concept riêng biệt, mang đậm dấu ấn sáng tạo.</p>
                    </div>
                </div>
                <div class="message-box box3">
                    <div class="box-content">
                        <h3>❤️ Sự kiện cảm xúc</h3>
                        <p>Kết nối cảm xúc, tạo nên trải nghiệm khó quên cho từng khách mời.</p>
                    </div>
                </div>
                <div class="message-box box4">
                    <div class="box-content">
                        <h3>📣 Truyền thông hiệu quả</h3>
                        <p>Lan tỏa thông điệp mạnh mẽ qua các kênh truyền thông chuyên nghiệp.</p>
                    </div>
                </div>
            </div>
            <div class="navigation">
                <div class="dot active" data-index="0"></div>
                <div class="dot" data-index="1"></div>
                <div class="dot" data-index="2"></div>
                <div class="dot" data-index="3"></div>
            </div>
        </section>
        <div class="about-section">
        <h2 class="text-center fw-bold fade-up title">Giới thiệu</h2>
            <div class="about-container">
                <div class="about-image fade-left">
                    <img src="https://scontent.fsgn19-1.fna.fbcdn.net/v/t39.30808-6/482240054_1194585768858839_5392605926008959326_n.jpg?_nc_cat=109&ccb=1-7&_nc_sid=cc71e4&_nc_ohc=wOWt_ZwQ1AsQ7kNvwEkPVFh&_nc_oc=Adk9bkIV9IWohxq_bhUQ_B6a72uPYZ7XyR8SvHGJsGMkTRGZPpaY8ZfCXzsZqdDRswM&_nc_zt=23&_nc_ht=scontent.fsgn19-1.fna&_nc_gid=08M_CYq8BWGA7mgzgYoQuA&oh=00_AfGlVaKrzGbjGNg9c2fw-4Csvgks6USDNNWNSdasD8AnUw&oe=67F8A6FE" alt="Hoang Huy Media Team">
                </div>
                <div class="about-content fade-up">
                    <h4>CÔNG TY TNHH THƯƠNG MẠI DỊCH VỤ SỰ KIỆN</h4>
                    <h2>NHỰT COMPUTER MEDIA</h2>
                    <p>
                        Công ty TNHH Thương Mại Dịch Vụ Sự Kiện EVENT (NHỰT COMPUTER  Media) là một công ty truyền thông uy tín tại Việt Nam, chuyên hoạt động trong các lĩnh vực như Media, Event, Activation, Teambuilding và Wedding.
                    </p>
                    <p>
                        Thành lập từ năm 2025, công ty đã tổ chức thành công nhiều sự kiện lớn như hội nghị, ra mắt sản phẩm, kỷ niệm thành lập, tiệc tất niên, khai trương, tri ân khách hàng và teambuilding.
                    </p>
                </div>
            </div>
        </div>
        <div class="container py-5">
            <h2 class="text-center fw-bold fade-up title">Địa Điểm tổ chức</h2>

            <div class="swiper mySwiper fade-left">
                <div class="swiper-wrapper">
                    <?php foreach ($venues as $venue): ?>
                        <div class="swiper-slide venue-slide" data-id="<?= htmlspecialchars($venue['VenueId']) ?>">
                            <div class="card venue-card">
                                <img src="/quanlysukien/images/<?= htmlspecialchars($venue['images']) ?>"
                                    class="card-img-top"
                                    alt="<?= htmlspecialchars($venue['VenueName']) ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold"><?= htmlspecialchars($venue['VenueName']) ?></h5>
                                    <p class="card-text"><i class="bi bi-geo-alt"></i> <?= htmlspecialchars($venue['Address']) ?></p>
                                    <p class="text-muted"><strong>Sức chứa:</strong> <?= htmlspecialchars($venue['Capacity']) ?> người</p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <div class="container py-5" style="margin-bottom: 100px">
            <h2 class="text-center fw-bold mt-5 fade-up title">Sự Kiện Sắp Tới</h2>
            <div class="swiper eventSwiper fade-left">
                <div class="swiper-wrapper">
                    <?php foreach ($events as $event): ?>
                        <div class="swiper-slide">
                            <div class="card venue-card">
                                <img src="/quanlysukien/images/<?= htmlspecialchars($event['images']) ?>" class="card-img-top" alt="<?= htmlspecialchars($event['EventName']) ?>">
                                <div class="card-body text-center">
                                    <h5 class="card-title fw-bold"><?= htmlspecialchars($event['EventName']) ?></h5>
                                    <p class="card-text"><?= htmlspecialchars($event['Description']) ?></p>
                                    <p><strong>Ngày tổ chức:</strong> <?= (new DateTime($event['EventDate']))->format('d-m-Y') ?></p>
                                    <p><strong>Địa điểm:</strong> <?= htmlspecialchars($event['VenueName']) ?></p>
                                    <p><strong>Loại sự kiện:</strong> <?= htmlspecialchars($event['TypeName']) ?></p>
                                    <p><strong>Người tổ chức:</strong> <?= htmlspecialchars($event['CreatedBy']) ?></p>
                                    <p><strong>Trạng thái:</strong> <?= ($event['status'] == 1) ? 'Đang diễn ra' : 'Sắp diễn ra' ?></p>
                                    <a href="event_details.php?id=<?= htmlspecialchars($event['EventId'] ?? '') ?>" class="btn btn-info">
                                        Xem Chi Tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="swiper-button-next"></div>
                <div class="swiper-button-prev"></div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
        <!-- Service Section -->
        <h2 class="title fade-up">DỊCH VỤ CUNG CẤP</h2>

        <div class="services">
            <div class="service-card fade-left" style="cursor: pointer;" onclick="window.location.href='http://localhost/Quanlysukien/View/Home/Venue.php'">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_aAUDNca9pKbA-MwuYtPtzvmY2aiQhr5dJw&s" alt="Cho Thuê Địa Điểm Tổ Chức">
                <h3>Thuê Địa Điểm Tổ Chức</h3>
            </div>


            <div class="service-card fade-left">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcQ6fXV9E3nnNtH1EUeXKCzrCOBj2-gJYumDQQ&s" alt="Cho Thuê Thiết Bị Sự Kiện">
                <h3>Thuê Thiết Bị Sự Kiện</h3>
            </div>

            <div class="service-card fade-left">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSLeZoUDbuBMojwtbGpEUu5P18efA_M5pChLg&s" alt="Decor">
                <h3>Decor</h3>
            </div>
        </div>
        <!-- Swiper JS -->
        <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
        <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
        <script>
            // Swiper cho địa điểm
            var swiper = new Swiper(".mySwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 2000,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
                pagination: {
                    el: ".swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2, // Hiển thị 2 địa điểm trên tablet
                    },
                    1024: {
                        slidesPerView: 3, // Hiển thị 3 địa điểm trên desktop
                    }
                }
            });
            // Swiper cho sự kiện
            var eventSwiper = new Swiper(".eventSwiper", {
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                autoplay: {
                    delay: 2500,
                    disableOnInteraction: false,
                },
                navigation: {
                    nextEl: ".eventSwiper .swiper-button-next",
                    prevEl: ".eventSwiper .swiper-button-prev",
                },
                pagination: {
                    el: ".eventSwiper .swiper-pagination",
                    clickable: true,
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                    },
                    1024: {
                        slidesPerView: 3,
                    }
                }
            });
            document.addEventListener('DOMContentLoaded', function() {
                const boxes = document.querySelectorAll('.message-box');
                const dots = document.querySelectorAll('.dot');
                let currentIndex = 0;
                let interval;

                // Hiển thị box đầu tiên khi trang load
                boxes[0].classList.add('active');

                // Hàm chuyển đổi giữa các box
                function showBox(index) {
                    // Ẩn tất cả các box
                    boxes.forEach(box => box.classList.remove('active'));
                    dots.forEach(dot => dot.classList.remove('active'));

                    // Hiển thị box hiện tại
                    boxes[index].classList.add('active');
                    dots[index].classList.add('active');

                    currentIndex = index;
                }

                // Tự động chuyển đổi
                function startAutoRotate() {
                    interval = setInterval(() => {
                        let nextIndex = (currentIndex + 1) % boxes.length;
                        showBox(nextIndex);
                    }, 3000); // Thay đổi mỗi 3 giây
                }

                // Khởi động tự động chuyển đổi
                startAutoRotate();

                // Xử lý sự kiện click vào các dots
                dots.forEach(dot => {
                    dot.addEventListener('click', function() {
                        // Dừng tự động chuyển đổi khi người dùng click
                        clearInterval(interval);

                        // Hiển thị box tương ứng
                        const index = parseInt(this.getAttribute('data-index'));
                        showBox(index);

                        // Khởi động lại tự động chuyển đổi
                        startAutoRotate();
                    });
                });
            });
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('fade-in');
                    } else {
                        entry.target.classList.remove('fade-in'); // cho phép hiệu ứng chạy lại
                    }
                });
            }, {
                threshold: 0.3
            });

            document.querySelectorAll('.fade-left, .fade-up').forEach(el => {
                observer.observe(el);
            });
        </script>
    </body>
    <?php include '../shares/footer.php'; ?>

    </html>