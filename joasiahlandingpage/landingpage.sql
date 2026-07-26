-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 24, 2026 at 08:29 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `landingpage`
--

-- --------------------------------------------------------

--
-- Table structure for table `customer_testimonials`
--

CREATE TABLE `customer_testimonials` (
  `testimonial_id` int(11) NOT NULL,
  `customer_full_name` varchar(150) DEFAULT NULL,
  `customer_profile_picture` varchar(255) DEFAULT NULL,
  `testimonial_message` text DEFAULT NULL,
  `testimonial_star_rating` int(11) DEFAULT NULL,
  `testimonial_visibility` enum('Visible','Hidden') DEFAULT 'Visible'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customer_testimonials`
--

INSERT INTO `customer_testimonials` (`testimonial_id`, `customer_full_name`, `customer_profile_picture`, `testimonial_message`, `testimonial_star_rating`, `testimonial_visibility`) VALUES
(1, 'John Cruz', 'assets/images/testimonials/customer1.jpg', 'The best cinema experience I have ever had. The sound system and seats are amazing!', 5, 'Visible'),
(2, 'Maria Santos', 'assets/images/testimonials/customer2.jpg', 'Very clean cinema and friendly staff. I will definitely come back.', 5, 'Visible'),
(3, 'Joshua Reyes', 'assets/images/testimonials/customer3.jpg', 'Watching movies here feels premium. Highly recommended!', 5, 'Visible');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `cinema_movie_id` int(11) NOT NULL,
  `movie_title` varchar(150) DEFAULT NULL,
  `movie_synopsis` text DEFAULT NULL,
  `movie_genre` varchar(100) DEFAULT NULL,
  `movie_duration_minutes` int(11) DEFAULT NULL,
  `movie_classification` varchar(20) DEFAULT NULL,
  `movie_release_date` date DEFAULT NULL,
  `movie_poster_image` varchar(255) DEFAULT NULL,
  `movie_background_image` varchar(255) DEFAULT NULL,
  `movie_trailer_video` varchar(255) DEFAULT NULL,
  `movie_ticket_price` decimal(10,2) DEFAULT NULL,
  `movie_showing_status` enum('Now Showing','Coming Soon','Unavailable') DEFAULT 'Now Showing',
  `hero_display_status` enum('Yes','No') DEFAULT 'No',
  `hero_display_caption` varchar(100) DEFAULT NULL,
  `movie_screening_schedule` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`cinema_movie_id`, `movie_title`, `movie_synopsis`, `movie_genre`, `movie_duration_minutes`, `movie_classification`, `movie_release_date`, `movie_poster_image`, `movie_background_image`, `movie_trailer_video`, `movie_ticket_price`, `movie_showing_status`, `hero_display_status`, `hero_display_caption`, `movie_screening_schedule`) VALUES
(1, 'Avengers: Infinity War', 'Thanos has arrived, and the Avengers must stand together like never before. From the streets of New York to far-off galaxies, every hero is drawn into a desperate battle to save the universe. Experience the highest-stakes showdown in the MCU, filled with epic action, shocking twists, and unforgettable moments.', 'Action, Adventure', 149, 'PG-13', '2018-04-27', 'assets/images/poster/image1.jpg', 'https://images.unsplash.com/photo-1489599849228-bed96c3ee601?w=1400&h=600&fit=crop', 'assets/trailer/aveengers.mp4', 350.00, 'Now Showing', 'Yes', 'ACTION PACKED', '2026-07-24 13:00:00'),
(2, 'The home', 'Dinosaurs once again roam the Earth in this epic new chapter of the Jurassic saga. Ancient creatures, new threats, and a race against time to save humanity from extinction. The park is gone. The world is now the cage. Experience the rebirth of an era.', 'Horror, Thriller', 134, 'PG-18', '2025-07-02', 'assets/images/poster/image2.jpg', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1400&h=600&fit=crop', 'assets/trailer/thehome.mp4', 350.00, 'Now Showing', 'Yes', 'THRILLING HORROR', '2026-07-24 15:30:00'),
(3, 'The Fantastic Four: First Steps', 'Marvel\'s first family discovers their extraordinary powers in this thrilling origin story. Four explorers, one cosmic accident, and a bond stronger than any superpower. Witness the beginning of a legendary superhero team that will change the world forever.', 'Action, Superhero', 126, 'PG-13', '2025-07-25', 'assets/images/poster/image8.jpg', 'https://images.unsplash.com/photo-1535016120720-40c646be5580?w=1400&h=600&fit=crop', 'assets/trailer/fantastic4.mp4', 350.00, 'Now Showing', 'Yes', 'HEROES RISE', '2026-07-24 18:00:00'),
(4, 'Love You Longtime', '\"Love You Long Time\" is a 2023 Philippine romantic drama film directed by JP Habac. The film stars Carlo Aquino and Eisel Serrano, and it revolves around the interaction of two characters in two different timelines via two-way radios.', 'romance, Drama', 134, 'PG-13', '2025-07-11', 'assets\\images\\poster\\image3.jpg', 'https://images.unsplash.com/photo-1489599849228-bed96c3ee601?w=1400&h=600&fit=crop', 'assets\\trailer\\loveyoulongtime.mp4', 350.00, 'Now Showing', 'Yes', 'Ready to Cry ', '2026-07-24 15:30:00'),
(5, 'Jurrasic World:Rebirth', 'et five years after Jurassic World Dominion, the Earth’s ecosystem has become largely inhospitable to dinosaurs, forcing the remaining creatures to survive in isolated equatorial regions with climates resembling their original habitats \r\n', 'Adventure', 149, 'PG-13', '2025-07-18', 'assets\\images\\poster\\image5.jpg', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1400&h=600&fit=crop', 'assets\\trailer\\jurassic.mp4', 350.00, 'Now Showing', 'No', 'ROAR', '2026-07-20 17:00:00'),
(6, 'The Sheep Detective', 'The Sheep Detectives follows a flock of unusually intelligent sheep who investigate the mysterious death of their shepherd, George Hardy, using the detective‑novel skills they secretly learned from his nightly readings.', 'Mystery', 149, 'PG-13', '2018-04-27', 'assets\\images\\poster\\image6.jpg', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1400&h=600&fit=crop', 'assets\\trailer\\sheepdetectives.mp4', 350.00, 'Now Showing', 'No', 'FIND THE TRUTH', '2026-07-20 17:00:00'),
(7, 'F1', 'Sonny Hayes (Brad Pitt), once a rising star in 1990s Formula One, had his career derailed by a severe crash at the Spanish Grand Prix in 1993, leaving him with lasting physical and emotional scars. Now living a nomadic life as a freelance racer, haunted by failed marriages and a gambling addiction,', 'Drama, Sport', 149, 'PG-13', '2022-04-27', 'assets\\images\\poster\\image7.jpg', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1400&h=600&fit=crop', 'assets\\trailer\\f1.mp4', 350.00, 'Now Showing', 'No', 'WHO WILL DOMINATE?', '2026-07-20 17:00:00'),
(8, 'Sputnik', 'Set in 1983 during the Cold War, the film follows cosmonauts Konstantin Veshnyakov and Kirill Averchenko on a space mission.', 'Horror', 134, 'PG-18', '2022-04-27', 'assets\\images\\poster\\image4.jpg', 'https://images.unsplash.com/photo-1536440136628-849c177e76a1?w=1400&h=600&fit=crop', 'assets\\trailer\\sputnik.mp4', 350.00, 'Now Showing', 'No', 'CAN YOU OUTRUN IT?', '2026-07-24 17:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `promotional_offers`
--

CREATE TABLE `promotional_offers` (
  `promotion_id` int(11) NOT NULL,
  `promotion_title` varchar(200) DEFAULT NULL,
  `promotion_description` text DEFAULT NULL,
  `promotion_banner_image` varchar(255) DEFAULT NULL,
  `promotion_button_caption` varchar(100) DEFAULT NULL,
  `promotion_display_order` int(11) DEFAULT NULL,
  `promotion_availability` enum('Active','Inactive') DEFAULT 'Active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `promotional_offers`
--

INSERT INTO `promotional_offers` (`promotion_id`, `promotion_title`, `promotion_description`, `promotion_banner_image`, `promotion_button_caption`, `promotion_display_order`, `promotion_availability`) VALUES
(1, 'Summer Blockbuster Pass', 'Watch any 5 movies this summer for only ₱1,499. Save up to 40% on regular ticket prices.', 'assets/images/promotions/promotion1.jpg', 'Claim Offer', 1, 'Active'),
(2, 'Family Night Special', 'Bring your family and enjoy 4 tickets + popcorn combo for just ₱999. Perfect for weekend outings!', 'assets/images/promotions/promotion2.png', 'Claim Offer', 2, 'Active');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `customer_testimonials`
--
ALTER TABLE `customer_testimonials`
  ADD PRIMARY KEY (`testimonial_id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`cinema_movie_id`);

--
-- Indexes for table `promotional_offers`
--
ALTER TABLE `promotional_offers`
  ADD PRIMARY KEY (`promotion_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `customer_testimonials`
--
ALTER TABLE `customer_testimonials`
  MODIFY `testimonial_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `cinema_movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `promotional_offers`
--
ALTER TABLE `promotional_offers`
  MODIFY `promotion_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
