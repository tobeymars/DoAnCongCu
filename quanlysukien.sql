-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th4 04, 2025 lúc 03:10 PM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `quanlysukien`
--
CREATE DATABASE IF NOT EXISTS `quanlysukien` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `quanlysukien`;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookingdetails`
--

CREATE TABLE `bookingdetails` (
  `BookingDetailId` int(11) NOT NULL,
  `BookingId` int(11) DEFAULT NULL,
  `TickettypeId` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `IsDeleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `bookings`
--

CREATE TABLE `bookings` (
  `BookingId` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `EventId` int(11) DEFAULT NULL,
  `BookingDate` datetime DEFAULT current_timestamp(),
  `Status` enum('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
  `IsDeleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `equipmentevents`
--

CREATE TABLE `equipmentevents` (
  `id` int(11) NOT NULL,
  `EventId` int(11) NOT NULL,
  `equipmentid` int(11) NOT NULL,
  `date` date NOT NULL,
  `soluong` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `equipments`
--

CREATE TABLE `equipments` (
  `EquipmentId` int(11) NOT NULL,
  `EquipmentTypeId` int(11) DEFAULT NULL,
  `EquipmentName` varchar(100) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Status` enum('Available','In Use','Damaged','Missing') DEFAULT 'Available',
  `IsDeleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `equipmenttypes`
--

CREATE TABLE `equipmenttypes` (
  `EquipmentTypeId` int(11) NOT NULL,
  `EquipmentTypeName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `events`
--

CREATE TABLE `events` (
  `EventId` int(11) NOT NULL,
  `EventName` varchar(100) NOT NULL,
  `Description` text DEFAULT NULL,
  `EventDate` datetime NOT NULL,
  `CreatedBy` int(11) DEFAULT NULL,
  `IsDeleted` tinyint(1) DEFAULT 0,
  `VenueId` int(11) DEFAULT NULL,
  `EventTypeId` int(11) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 0,
  `images` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `eventtypes`
--

CREATE TABLE `eventtypes` (
  `EventTypeId` int(11) NOT NULL,
  `TypeName` varchar(100) NOT NULL,
  `IsDeleted` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `payments`
--

CREATE TABLE `payments` (
  `PaymentId` int(11) NOT NULL,
  `BookingId` int(11) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `PaymentMethod` enum('Thẻ tín dụng','Tiền mặt','Chuyển khoản ngân hàng') DEFAULT NULL,
  `PaymentDate` datetime DEFAULT current_timestamp(),
  `IsDeleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `roles`
--

CREATE TABLE `roles` (
  `RoleId` int(11) NOT NULL,
  `RoleName` enum('Admin','User','Event Organizer') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Đang đổ dữ liệu cho bảng `roles`
--

INSERT INTO `roles` (`RoleId`, `RoleName`) VALUES
(1, 'Admin'),
(2, 'User'),
(3, 'Event Organizer');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tickettypes`
--

CREATE TABLE `tickettypes` (
  `TicketTypeId` int(11) NOT NULL,
  `EventId` int(11) DEFAULT NULL,
  `TicketName` varchar(50) DEFAULT NULL,
  `Price` decimal(10,2) DEFAULT NULL,
  `IsDeleted` tinyint(1) DEFAULT 0,
  `Quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `UserId` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `FullName` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `RoleId` int(11) DEFAULT NULL,
  `IsDeleted` tinyint(1) DEFAULT 0,
  `sdt` varchar(11) NOT NULL,
  `images` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `venues`
--

CREATE TABLE `venues` (
  `VenueId` int(11) NOT NULL,
  `VenueName` varchar(100) NOT NULL,
  `Address` varchar(255) NOT NULL,
  `Capacity` int(11) NOT NULL,
  `Description` text DEFAULT NULL,
  `Status` enum('Available','Booked','Under Maintenance') DEFAULT 'Available',
  `IsDeleted` tinyint(1) DEFAULT 0,
  `images` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `bookingdetails`
--
ALTER TABLE `bookingdetails`
  ADD PRIMARY KEY (`BookingDetailId`),
  ADD KEY `BookingId` (`BookingId`),
  ADD KEY `bookingdetails_ibfk_2` (`TickettypeId`);

--
-- Chỉ mục cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`BookingId`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `EventId` (`EventId`);

--
-- Chỉ mục cho bảng `equipments`
--
ALTER TABLE `equipments`
  ADD PRIMARY KEY (`EquipmentId`),
  ADD KEY `EquipmentTypeId` (`EquipmentTypeId`);

--
-- Chỉ mục cho bảng `equipmenttypes`
--
ALTER TABLE `equipmenttypes`
  ADD PRIMARY KEY (`EquipmentTypeId`);

--
-- Chỉ mục cho bảng `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`EventId`),
  ADD KEY `CreatedBy` (`CreatedBy`),
  ADD KEY `VenueId` (`VenueId`),
  ADD KEY `FK_Events_EventType` (`EventTypeId`);

--
-- Chỉ mục cho bảng `eventtypes`
--
ALTER TABLE `eventtypes`
  ADD PRIMARY KEY (`EventTypeId`),
  ADD UNIQUE KEY `TypeName` (`TypeName`);

--
-- Chỉ mục cho bảng `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`PaymentId`),
  ADD KEY `BookingId` (`BookingId`);

--
-- Chỉ mục cho bảng `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`RoleId`),
  ADD UNIQUE KEY `RoleName` (`RoleName`);

--
-- Chỉ mục cho bảng `tickettypes`
--
ALTER TABLE `tickettypes`
  ADD PRIMARY KEY (`TicketTypeId`),
  ADD KEY `EventId` (`EventId`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserId`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD KEY `RoleId` (`RoleId`);

--
-- Chỉ mục cho bảng `venues`
--
ALTER TABLE `venues`
  ADD PRIMARY KEY (`VenueId`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `bookingdetails`
--
ALTER TABLE `bookingdetails`
  MODIFY `BookingDetailId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `bookings`
--
ALTER TABLE `bookings`
  MODIFY `BookingId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `equipments`
--
ALTER TABLE `equipments`
  MODIFY `EquipmentId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `equipmenttypes`
--
ALTER TABLE `equipmenttypes`
  MODIFY `EquipmentTypeId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `events`
--
ALTER TABLE `events`
  MODIFY `EventId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `eventtypes`
--
ALTER TABLE `eventtypes`
  MODIFY `EventTypeId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `payments`
--
ALTER TABLE `payments`
  MODIFY `PaymentId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `roles`
--
ALTER TABLE `roles`
  MODIFY `RoleId` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT cho bảng `tickettypes`
--
ALTER TABLE `tickettypes`
  MODIFY `TicketTypeId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `UserId` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `venues`
--
ALTER TABLE `venues`
  MODIFY `VenueId` int(11) NOT NULL AUTO_INCREMENT;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `bookingdetails`
--
ALTER TABLE `bookingdetails`
  ADD CONSTRAINT `bookingdetails_ibfk_1` FOREIGN KEY (`BookingId`) REFERENCES `bookings` (`BookingId`),
  ADD CONSTRAINT `bookingdetails_ibfk_2` FOREIGN KEY (`TickettypeId`) REFERENCES `tickettypes` (`TicketTypeId`);

--
-- Các ràng buộc cho bảng `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `users` (`UserId`),
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`EventId`) REFERENCES `events` (`EventId`);

--
-- Các ràng buộc cho bảng `equipments`
--
ALTER TABLE `equipments`
  ADD CONSTRAINT `equipments_ibfk_1` FOREIGN KEY (`EquipmentTypeId`) REFERENCES `equipmenttypes` (`EquipmentTypeId`);

--
-- Các ràng buộc cho bảng `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `FK_Events_EventType` FOREIGN KEY (`EventTypeId`) REFERENCES `eventtypes` (`EventTypeId`),
  ADD CONSTRAINT `events_ibfk_1` FOREIGN KEY (`CreatedBy`) REFERENCES `users` (`UserId`),
  ADD CONSTRAINT `events_ibfk_2` FOREIGN KEY (`VenueId`) REFERENCES `venues` (`VenueId`);

--
-- Các ràng buộc cho bảng `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`BookingId`) REFERENCES `bookings` (`BookingId`);

--
-- Các ràng buộc cho bảng `tickettypes`
--
ALTER TABLE `tickettypes`
  ADD CONSTRAINT `tickettypes_ibfk_1` FOREIGN KEY (`EventId`) REFERENCES `events` (`EventId`);

--
-- Các ràng buộc cho bảng `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`RoleId`) REFERENCES `roles` (`RoleId`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
