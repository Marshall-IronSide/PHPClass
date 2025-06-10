-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3377
-- Generation Time: Jun 10, 2025 at 04:45 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `books_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@bookstore.com', '2025-06-04 21:02:17');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `isbn` varchar(20) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `stock_quantity` int(11) DEFAULT 0,
  `category_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image_url` varchar(500) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `isbn`, `price`, `stock_quantity`, `category_id`, `description`, `image_url`, `featured`, `created_at`) VALUES
(1, 'The Great Gatsby', 'F. Scott Fitzgerald', '9780743273565', 12.99, 25, 1, 'Un roman américain classique de l\'âge du jazz', 'https://covers.openlibrary.org/b/isbn/9780743273565-L.jpg', 1, '2025-06-04 21:02:17'),
(2, 'To Kill a Mockingbird', 'Harper Lee', '9780446310789', 14.99, 30, 1, 'Un récit saisissant d\'injustice raciale et d\'enfance perdue', 'https://covers.openlibrary.org/b/isbn/9780446310789-L.jpg', 1, '2025-06-04 21:02:17'),
(3, '1984', 'George Orwell', '9780451524935', 13.99, 20, 1, 'Un roman dystopique de science-fiction sociale', 'https://covers.openlibrary.org/b/isbn/9780451524935-L.jpg', 0, '2025-06-04 21:02:17'),
(4, 'Dune', 'Frank Herbert', '9780441172719', 16.99, 15, 3, 'Un roman épique de science-fiction situé dans un futur lointain', 'https://covers.openlibrary.org/b/isbn/9780441172719-L.jpg', 1, '2025-06-04 21:02:17'),
(5, 'The Martian', 'Andy Weir', '9780553418026', 15.99, 18, 3, 'Une histoire de survie sur Mars', 'https://covers.openlibrary.org/b/isbn/9780553418026-L.jpg', 0, '2025-06-04 21:02:17'),
(6, 'Gone Girl', 'Gillian Flynn', '9780307588371', 14.99, 22, 4, 'Un thriller psychologique sur une épouse disparue', 'https://covers.openlibrary.org/b/isbn/9780307588371-L.jpg', 0, '2025-06-04 21:02:17'),
(7, 'The Girl with the Dragon Tattoo', 'Stieg Larsson', '9780307454546', 15.99, 12, 4, 'Un thriller criminel suédois', 'https://covers.openlibrary.org/b/isbn/9780307454546-L.jpg', 0, '2025-06-04 21:02:17'),
(8, 'Pride and Prejudice', 'Jane Austen', '9780141439518', 11.99, 35, 5, 'Un roman d\'amour classique', 'https://covers.openlibrary.org/b/isbn/9780141439518-L.jpg', 1, '2025-06-04 21:02:17'),
(9, 'Steve Jobs', 'Walter Isaacson', '9781451648539', 18.99, 10, 2, 'Biographie du co-fondateur d\'Apple', 'https://covers.openlibrary.org/b/isbn/9781451648539-L.jpg', 0, '2025-06-04 21:02:17'),
(10, 'Think and Grow Rich', 'Napoleon Hill', '9781585424337', 12.99, 28, 8, 'Livre classique d\'auto-assistance sur le succès', 'https://covers.openlibrary.org/b/isbn/9781585424337-L.jpg', 0, '2025-06-04 21:02:17'),
(11, 'Clean Code', 'Robert C. Martin', '9780132350884', 24.99, 15, 7, 'Un manuel sur l\'artisanat logiciel agile', 'https://covers.openlibrary.org/b/isbn/9780132350884-L.jpg', 0, '2025-06-04 21:02:17'),
(12, 'The Lean Startup', 'Eric Ries', '9780307887894', 16.99, 20, 6, 'Comment l\'innovation crée des entreprises radicalement prospères', 'https://covers.openlibrary.org/b/isbn/9780307887894-L.jpg', 0, '2025-06-04 21:02:17'),
(13, 'Harry Potter and the Philosopher\'s Stone', 'J.K. Rowling', '9780747532699', 12.99, 50, 1, 'Le premier livre de la série magique Harry Potter', 'https://covers.openlibrary.org/b/isbn/9780747532699-L.jpg', 1, '2025-06-04 21:02:17'),
(14, 'The Hobbit', 'J.R.R. Tolkien', '9780547928227', 14.99, 35, 1, 'Un conte d\'aventure fantastique classique', 'https://covers.openlibrary.org/b/isbn/9780547928227-L.jpg', 1, '2025-06-04 21:02:17'),
(15, 'Sapiens: A Brief History of Humankind', 'Yuval Noah Harari', '9780062316097', 17.99, 25, 8, 'Un regard fascinant sur l\'évolution humaine et la société', 'https://covers.openlibrary.org/b/isbn/9780062316097-L.jpg', 1, '2025-06-04 21:02:17'),
(16, 'The Catcher in the Rye', 'J.D. Salinger', '9780316769174', 13.99, 30, 1, 'Une histoire de passage à l\'âge adulte devenue phénomène culturel', 'https://covers.openlibrary.org/b/isbn/9780316769174-L.jpg', 0, '2025-06-04 21:02:17'),
(17, 'Atomic Habits', 'James Clear', '9780735211292', 16.99, 40, 8, 'Un moyen facile et prouvé de construire de bonnes habitudes et d\'éliminer les mauvaises', 'https://covers.openlibrary.org/b/isbn/9780735211292-L.jpg', 1, '2025-06-04 21:02:17'),
(18, 'The Da Vinci Code', 'Dan Brown', '9780307474278', 15.99, 20, 4, 'Un thriller captivant mêlant art, religion et conspiration', 'https://covers.openlibrary.org/b/isbn/9780307474278-L.jpg', 0, '2025-06-04 21:02:17'),
(19, 'The Alchemist', 'Paulo Coelho', '9780061122415', 14.99, 45, 1, 'Un roman philosophique sur la poursuite de ses rêves', 'https://covers.openlibrary.org/b/isbn/9780061122415-L.jpg', 1, '2025-06-04 21:02:17'),
(20, 'The Fellowship of the Ring', 'J.R.R. Tolkien', '9780547928210', 16.99, 25, 1, 'Le premier volume de l\'épique trilogie du Seigneur des Anneaux', 'https://covers.openlibrary.org/b/isbn/9780547928210-L.jpg', 1, '2025-06-04 21:02:17'),
(21, 'The 7 Habits of Highly Effective People', 'Stephen R. Covey', '9781982137274', 18.99, 30, 8, 'Une puissante leçon de changement personnel', 'https://covers.openlibrary.org/b/isbn/9781982137274-L.jpg', 0, '2025-06-04 21:02:17'),
(22, 'Becoming', 'Michelle Obama', '9781524763138', 19.99, 35, 2, 'Mémoires intimes de l\'ancienne Première Dame Michelle Obama', 'https://covers.openlibrary.org/b/isbn/9781524763138-L.jpg', 1, '2025-06-04 21:02:17'),
(23, 'The Subtle Art of Not Giving a F*ck', 'Mark Manson', '9780062457714', 15.99, 40, 8, 'Une approche contre-intuitive pour vivre une bonne vie', 'https://covers.openlibrary.org/b/isbn/9780062457714-L.jpg', 0, '2025-06-04 21:02:17'),
(24, 'Educated', 'Tara Westover', '9780399590504', 17.99, 28, 2, 'Mémoires puissantes sur l\'éducation, la famille et la lutte pour l\'auto-invention', 'https://covers.openlibrary.org/b/isbn/9780399590504-L.jpg', 1, '2025-06-04 21:02:17'),
(25, 'The Handmaid\'s Tale', 'Margaret Atwood', '9780385490818', 15.99, 22, 1, 'Un roman dystopique sur une société totalitaire', 'https://covers.openlibrary.org/b/isbn/9780385490818-L.jpg', 0, '2025-06-04 21:02:17'),
(26, 'Where the Crawdads Sing', 'Delia Owens', '9780735219090', 16.99, 33, 1, 'Un mystère et histoire de passage à l\'âge adulte dans les marais de Caroline du Nord', 'https://covers.openlibrary.org/b/isbn/9780735219090-L.jpg', 1, '2025-06-04 21:02:17'),
(27, 'The Psychology of Money', 'Morgan Housel', '9780857197689', 16.99, 26, 6, 'Leçons intemporelles sur la richesse, la cupidité et le bonheur', 'https://covers.openlibrary.org/b/isbn/9780857197689-L.jpg', 0, '2025-06-04 21:02:17'),
(28, 'Dune Messiah', 'Frank Herbert', '9780441172696', 15.99, 18, 3, 'Le deuxième livre de l\'épique saga Dune', 'https://covers.openlibrary.org/b/isbn/9780441172696-L.jpg', 0, '2025-06-04 21:02:17'),
(29, 'The Silent Patient', 'Alex Michaelides', '9781250301703', 14.99, 24, 4, 'Un thriller psychologique sur une femme qui refuse de parler', 'https://covers.openlibrary.org/b/isbn/9781250301703-L.jpg', 1, '2025-06-04 21:02:17'),
(30, 'Can\'t Hurt Me', 'David Goggins', '9781544512273', 18.99, 31, 8, 'Maîtrise ton esprit et défie les obstacles', 'https://covers.openlibrary.org/b/isbn/9781544512273-L.jpg', 0, '2025-06-04 21:02:17'),
(31, 'Fifty Shades of Grey', 'E.L. James', '9780345803481', 13.99, 25, 5, 'Un roman d\'amour passionné', 'https://covers.openlibrary.org/b/isbn/9780345803481-L.jpg', 0, '2025-06-04 21:02:17'),
(32, 'The Hunger Games', 'Suzanne Collins', '9780439023528', 12.99, 40, 1, 'Un roman dystopique pour jeunes adultes', 'https://covers.openlibrary.org/b/isbn/9780439023528-L.jpg', 1, '2025-06-04 21:02:17'),
(33, 'Twilight', 'Stephenie Meyer', '9780316015844', 14.99, 35, 5, 'Un roman d\'amour avec des vampires', 'https://covers.openlibrary.org/b/isbn/9780316015844-L.jpg', 0, '2025-06-04 21:02:17'),
(34, 'The Fault in Our Stars', 'John Green', '9780525478812', 13.99, 30, 1, 'Une romance déchirante pour jeunes adultes', 'https://covers.openlibrary.org/b/isbn/9780525478812-L.jpg', 1, '2025-06-04 21:02:17'),
(35, 'Life of Pi', 'Yann Martel', '9780156027328', 15.99, 22, 1, 'Une histoire de survie d\'un garçon et d\'un tigre', 'https://covers.openlibrary.org/b/isbn/9780156027328-L.jpg', 0, '2025-06-04 21:02:17'),
(36, 'The Kite Runner', 'Khaled Hosseini', '9781594631931', 16.99, 28, 1, 'Une histoire puissante d\'amitié et de rédemption', 'https://covers.openlibrary.org/b/isbn/9781594631931-L.jpg', 1, '2025-06-04 21:02:17'),
(37, 'The Book Thief', 'Markus Zusak', '9780375842207', 14.99, 33, 1, 'Une histoire narrée par la Mort pendant la Seconde Guerre mondiale', 'https://covers.openlibrary.org/b/isbn/9780375842207-L.jpg', 1, '2025-06-04 21:02:17'),
(38, 'One Hundred Years of Solitude', 'Gabriel García Márquez', '9780060883287', 17.99, 20, 1, 'Un chef-d\'œuvre du réalisme magique', 'https://covers.openlibrary.org/b/isbn/9780060883287-L.jpg', 0, '2025-06-04 21:02:17'),
(39, 'Brave New World', 'Aldous Huxley', '9780060850524', 14.99, 26, 1, 'Un roman dystopique de science-fiction sociale', 'https://covers.openlibrary.org/b/isbn/9780060850524-L.jpg', 0, '2025-06-04 21:02:17'),
(40, 'The Road', 'Cormac McCarthy', '9780307387899', 15.99, 19, 1, 'Un récit post-apocalyptique de survie', 'https://covers.openlibrary.org/b/isbn/9780307387899-L.jpg', 0, '2025-06-04 21:02:17'),
(41, 'The Girl on the Train', 'Paula Hawkins', '9781594633669', 15.99, 32, 4, 'Un thriller psychologique sur l\'obsession', 'https://covers.openlibrary.org/b/isbn/9781594633669-L.jpg', 1, '2025-06-04 21:02:17'),
(42, 'Big Little Lies', 'Liane Moriarty', '9780399167065', 16.99, 29, 4, 'Un mystère impliquant trois femmes', 'https://covers.openlibrary.org/b/isbn/9780399167065-L.jpg', 0, '2025-06-04 21:02:17'),
(43, 'The Shining', 'Stephen King', '9780385121675', 17.99, 21, 4, 'Un roman d\'horreur sur l\'isolement et la folie', 'https://covers.openlibrary.org/b/isbn/9780385121675-L.jpg', 0, '2025-06-04 21:02:17'),
(44, 'It', 'Stephen King', '9781501142970', 19.99, 18, 4, 'Un roman d\'horreur sur une entité qui change de forme', 'https://covers.openlibrary.org/b/isbn/9781501142970-L.jpg', 1, '2025-06-04 21:02:17'),
(45, 'Sherlock Holmes Complete', 'Arthur Conan Doyle', '9780486227528', 16.99, 25, 4, 'Les aventures complètes de Sherlock Holmes', 'https://covers.openlibrary.org/b/isbn/9780486227528-L.jpg', 0, '2025-06-04 21:02:17'),
(46, 'And Then There Were None', 'Agatha Christie', '9780062073488', 14.99, 35, 4, 'Un mystère classique sur une île isolée', 'https://covers.openlibrary.org/b/isbn/9780062073488-L.jpg', 1, '2025-06-04 21:02:17'),
(47, 'The Silence of the Lambs', 'Thomas Harris', '9780312924584', 15.99, 23, 4, 'Un thriller d\'horreur psychologique', 'https://covers.openlibrary.org/b/isbn/9780312924584-L.jpg', 0, '2025-06-04 21:02:17'),
(48, 'In the Woods', 'Tana French', '9780143113492', 16.99, 27, 4, 'Un mystère atmosphérique situé en Irlande', 'https://covers.openlibrary.org/b/isbn/9780143113492-L.jpg', 0, '2025-06-04 21:02:17'),
(49, 'The Talented Mr. Ripley', 'Patricia Highsmith', '9780393332148', 15.99, 24, 4, 'Un thriller psychologique sur l\'identité', 'https://covers.openlibrary.org/b/isbn/9780393332148-L.jpg', 0, '2025-06-04 21:02:17'),
(50, 'Rebecca', 'Daphne du Maurier', '9780380730407', 14.99, 31, 4, 'Un roman gothique et thriller psychologique', 'https://covers.openlibrary.org/b/isbn/9780380730407-L.jpg', 1, '2025-06-04 21:02:17'),
(51, 'Foundation', 'Isaac Asimov', '9780553293357', 16.99, 26, 3, 'Le premier livre de la série Foundation', 'https://covers.openlibrary.org/b/isbn/9780553293357-L.jpg', 1, '2025-06-04 21:02:17'),
(52, 'Ender\'s Game', 'Orson Scott Card', '9780812550702', 15.99, 34, 3, 'Un roman de science-fiction militaire', 'https://covers.openlibrary.org/b/isbn/9780812550702-L.jpg', 1, '2025-06-04 21:02:17'),
(53, 'Neuromancer', 'William Gibson', '9780441569595', 17.99, 19, 3, 'Un roman de science-fiction cyberpunk', 'https://covers.openlibrary.org/b/isbn/9780441569595-L.jpg', 0, '2025-06-04 21:02:17'),
(54, 'The Hitchhiker\'s Guide to the Galaxy', 'Douglas Adams', '9780345391803', 13.99, 42, 3, 'Une série de science-fiction comique', 'https://covers.openlibrary.org/b/isbn/9780345391803-L.jpg', 1, '2025-06-04 21:02:17'),
(55, 'Game of Thrones', 'George R.R. Martin', '9780553103540', 18.99, 37, 1, 'Le premier livre du Trône de fer', 'https://covers.openlibrary.org/b/isbn/9780553103540-L.jpg', 1, '2025-06-04 21:02:17'),
(56, 'The Name of the Wind', 'Patrick Rothfuss', '9780756404079', 16.99, 29, 1, 'Le premier livre de la Chronique du Tueur de roi', 'https://covers.openlibrary.org/b/isbn/9780756404079-L.jpg', 0, '2025-06-04 21:02:17'),
(57, 'American Gods', 'Neil Gaiman', '9780380789030', 17.99, 23, 1, 'Un roman fantastique sur les dieux en Amérique', 'https://covers.openlibrary.org/b/isbn/9780380789030-L.jpg', 0, '2025-06-04 21:02:17'),
(58, 'The Stand', 'Stephen King', '9780307743688', 19.99, 16, 3, 'Un roman de dark fantasy post-apocalyptique', 'https://covers.openlibrary.org/b/isbn/9780307743688-L.jpg', 0, '2025-06-04 21:02:17'),
(59, 'Ready Player One', 'Ernest Cline', '9780307887436', 15.99, 38, 3, 'Un roman de science-fiction dystopique', 'https://covers.openlibrary.org/b/isbn/9780307887436-L.jpg', 1, '2025-06-04 21:02:17'),
(60, 'The Left Hand of Darkness', 'Ursula K. Le Guin', '9780441478125', 16.99, 21, 3, 'Un roman de science-fiction révolutionnaire', 'https://covers.openlibrary.org/b/isbn/9780441478125-L.jpg', 0, '2025-06-04 21:02:17'),
(61, 'Rich Dad Poor Dad', 'Robert Kiyosaki', '9781612680194', 17.99, 45, 6, 'Ce que les riches enseignent à leurs enfants sur l\'argent', 'https://covers.openlibrary.org/b/isbn/9781612680194-L.jpg', 1, '2025-06-04 21:02:17'),
(62, 'How to Win Friends and Influence People', 'Dale Carnegie', '9780671027032', 16.99, 41, 8, 'Le livre classique d\'amélioration personnelle', 'https://covers.openlibrary.org/b/isbn/9780671027032-L.jpg', 1, '2025-06-04 21:02:17'),
(63, 'The Power of Now', 'Eckhart Tolle', '9781577314806', 15.99, 33, 8, 'Un guide vers l\'illumination spirituelle', 'https://covers.openlibrary.org/b/isbn/9781577314806-L.jpg', 0, '2025-06-04 21:02:17'),
(64, 'Good to Great', 'Jim Collins', '9780066620992', 18.99, 27, 6, 'Pourquoi certaines entreprises font le saut et d\'autres pas', 'https://covers.openlibrary.org/b/isbn/9780066620992-L.jpg', 0, '2025-06-04 21:02:17'),
(65, 'The 4-Hour Workweek', 'Timothy Ferriss', '9780307465351', 17.99, 35, 6, 'Échapper au 9h-17h et vivre n\'importe où', 'https://covers.openlibrary.org/b/isbn/9780307465351-L.jpg', 1, '2025-06-04 21:02:17'),
(66, 'Mindset', 'Carol S. Dweck', '9780345472328', 16.99, 39, 8, 'La nouvelle psychologie du succès', 'https://covers.openlibrary.org/b/isbn/9780345472328-L.jpg', 0, '2025-06-04 21:02:17'),
(67, 'The Millionaire Next Door', 'Thomas J. Stanley', '9781589795471', 17.99, 25, 6, 'Les secrets surprenants des riches américains', 'https://covers.openlibrary.org/b/isbn/9781589795471-L.jpg', 0, '2025-06-04 21:02:17'),
(68, 'Emotional Intelligence', 'Daniel Goleman', '9780553383713', 16.99, 31, 8, 'Pourquoi c\'est plus important que le QI', 'https://covers.openlibrary.org/b/isbn/9780553383713-L.jpg', 0, '2025-06-04 21:02:17'),
(69, 'Start With Why', 'Simon Sinek', '9781591846444', 17.99, 28, 6, 'Comment les grands leaders inspirent chacun à agir', 'https://covers.openlibrary.org/b/isbn/9781591846444-L.jpg', 1, '2025-06-04 21:02:17'),
(70, 'The Compound Effect', 'Darren Hardy', '9781593157241', 15.99, 32, 8, 'Booster vos revenus, votre vie et votre succès', 'https://covers.openlibrary.org/b/isbn/9781593157241-L.jpg', 0, '2025-06-04 21:02:17'),
(71, 'Einstein: His Life and Universe', 'Walter Isaacson', '9780743264747', 19.99, 22, 2, 'Une biographie complète d\'Einstein', 'https://covers.openlibrary.org/b/isbn/9780743264747-L.jpg', 0, '2025-06-04 21:02:17'),
(72, 'The Diary of a Young Girl', 'Anne Frank', '9780553296983', 12.99, 47, 2, 'Le célèbre journal d\'Anne Frank', 'https://covers.openlibrary.org/b/isbn/9780553296983-L.jpg', 1, '2025-06-04 21:02:17'),
(73, 'Long Walk to Freedom', 'Nelson Mandela', '9780316548182', 18.99, 24, 2, 'L\'autobiographie de Nelson Mandela', 'https://covers.openlibrary.org/b/isbn/9780316548182-L.jpg', 1, '2025-06-04 21:02:17'),
(74, 'The Immortal Life of Henrietta Lacks', 'Rebecca Skloot', '9781400052189', 16.99, 29, 2, 'L\'histoire derrière les cellules HeLa', 'https://covers.openlibrary.org/b/isbn/9781400052189-L.jpg', 0, '2025-06-04 21:02:17'),
(75, 'Alexander Hamilton', 'Ron Chernow', '9780143034759', 19.99, 18, 2, 'Biographie d\'Alexander Hamilton', 'https://covers.openlibrary.org/b/isbn/9780143034759-L.jpg', 0, '2025-06-04 21:02:17'),
(76, 'Guns, Germs, and Steel', 'Jared Diamond', '9780393317558', 18.99, 26, 8, 'Le destin des sociétés humaines', 'https://covers.openlibrary.org/b/isbn/9780393317558-L.jpg', 0, '2025-06-04 21:02:17'),
(77, 'A Brief History of Time', 'Stephen Hawking', '9780553380163', 17.99, 35, 8, 'Du Big Bang aux trous noirs', 'https://covers.openlibrary.org/b/isbn/9780553380163-L.jpg', 1, '2025-06-04 21:02:17'),
(78, 'The Wright Brothers', 'David McCullough', '9781476728742', 18.99, 23, 2, 'L\'histoire des pionniers de l\'aviation', 'https://covers.openlibrary.org/b/isbn/9781476728742-L.jpg', 0, '2025-06-04 21:02:17'),
(79, 'John Adams', 'David McCullough', '9780743223133', 19.99, 20, 2, 'Biographie du deuxième président américain', 'https://covers.openlibrary.org/b/isbn/9780743223133-L.jpg', 0, '2025-06-04 21:02:17'),
(80, 'The Devil Wears Prada', 'Lauren Weisberger', '9780767914765', 14.99, 36, 1, 'Un roman sur l\'industrie de la mode', 'https://covers.openlibrary.org/b/isbn/9780767914765-L.jpg', 0, '2025-06-04 21:02:17'),
(81, 'War and Peace', 'Leo Tolstoy', '9780307266934', 21.99, 15, 1, 'Roman russe épique sur les guerres napoléoniennes', 'https://covers.openlibrary.org/b/isbn/9780307266934-L.jpg', 0, '2025-06-04 21:02:17'),
(82, 'Crime and Punishment', 'Fyodor Dostoevsky', '9780486415871', 16.99, 25, 1, 'Un drame psychologique sur la culpabilité', 'https://covers.openlibrary.org/b/isbn/9780486415871-L.jpg', 0, '2025-06-04 21:02:17'),
(83, 'Jane Eyre', 'Charlotte Brontë', '9780141441146', 14.99, 30, 1, 'Un roman gothique d\'amour', 'https://covers.openlibrary.org/b/isbn/9780141441146-L.jpg', 1, '2025-06-04 21:02:17'),
(84, 'Wuthering Heights', 'Emily Brontë', '9780141439556', 14.99, 28, 1, 'Un récit de passion et de vengeance', 'https://covers.openlibrary.org/b/isbn/9780141439556-L.jpg', 0, '2025-06-04 21:02:17'),
(85, 'Moby Dick', 'Herman Melville', '9780142437247', 17.99, 19, 1, 'L\'histoire du capitaine Achab et de la baleine blanche', 'https://covers.openlibrary.org/b/isbn/9780142437247-L.jpg', 0, '2025-06-04 21:02:17'),
(86, 'The Count of Monte Cristo', 'Alexandre Dumas', '9780140449266', 18.99, 22, 1, 'Un récit de vengeance et de rédemption', 'https://covers.openlibrary.org/b/isbn/9780140449266-L.jpg', 1, '2025-06-04 21:02:17'),
(87, 'Les Misérables', 'Victor Hugo', '9780451419439', 19.99, 17, 1, 'Roman historique français épique', 'https://covers.openlibrary.org/b/isbn/9780451419439-L.jpg', 0, '2025-06-04 21:02:17'),
(88, 'Don Quixote', 'Miguel de Cervantes', '9780060934347', 18.99, 21, 1, 'Les aventures d\'un chevalier idéaliste', 'https://covers.openlibrary.org/b/isbn/9780060934347-L.jpg', 0, '2025-06-04 21:02:17'),
(89, 'The Adventures of Huckleberry Finn', 'Mark Twain', '9780486280615', 12.99, 33, 1, 'Histoire américaine classique de passage à l\'âge adulte', 'https://covers.openlibrary.org/b/isbn/9780486280615-L.jpg', 0, '2025-06-04 21:02:17'),
(90, 'Frankenstein', 'Mary Shelley', '9780486282114', 13.99, 31, 1, 'Le roman d\'horreur de science-fiction original', 'https://covers.openlibrary.org/b/isbn/9780486282114-L.jpg', 1, '2025-06-04 21:02:17'),
(91, 'The Time Traveler\'s Wife', 'Audrey Niffenegger', '9780156029438', 16.99, 27, 5, 'Une histoire d\'amour à travers le temps', 'https://covers.openlibrary.org/b/isbn/9780156029438-L.jpg', 1, '2025-06-04 21:02:17'),
(92, 'The Lovely Bones', 'Alice Sebold', '9780316168816', 15.99, 29, 1, 'Une histoire racontée depuis le paradis', 'https://covers.openlibrary.org/b/isbn/9780316168816-L.jpg', 0, '2025-06-04 21:02:17'),
(93, 'Water for Elephants', 'Sara Gruen', '9781565125605', 15.99, 32, 1, 'Une histoire de cirque pendant la Grande Dépression', 'https://covers.openlibrary.org/b/isbn/9781565125605-L.jpg', 0, '2025-06-04 21:02:17'),
(94, 'The Help', 'Kathryn Stockett', '9780425232200', 16.99, 34, 1, 'Une histoire sur les tensions raciales dans le Mississippi des années 1960', 'https://covers.openlibrary.org/b/isbn/9780425232200-L.jpg', 1, '2025-06-04 21:02:17'),
(95, 'Eat, Pray, Love', 'Elizabeth Gilbert', '9780143038412', 15.99, 38, 2, 'Le voyage de découverte de soi d\'une femme', 'https://covers.openlibrary.org/b/isbn/9780143038412-L.jpg', 0, '2025-06-04 21:02:17'),
(96, 'The Secret Garden', 'Frances Hodgson Burnett', '9780486411934', 11.99, 41, 1, 'Un classique pour enfants sur la guérison et la croissance', 'https://covers.openlibrary.org/b/isbn/9780486411934-L.jpg', 0, '2025-06-04 21:02:17'),
(97, 'Charlotte\'s Web', 'E.B. White', '9780064400558', 9.99, 48, 1, 'Une histoire bien-aimée pour enfants', 'https://covers.openlibrary.org/b/isbn/9780064400558-L.jpg', 1, '2025-06-04 21:02:17'),
(98, 'The Lion, the Witch and the Wardrobe', 'C.S. Lewis', '9780064404990', 10.99, 45, 1, 'La première aventure de Narnia', 'https://covers.openlibrary.org/b/isbn/9780064404990-L.jpg', 1, '2025-06-04 21:02:17'),
(99, 'A Wrinkle in Time', 'Madeleine L\'Engle', '9780312367541', 12.99, 37, 1, 'Une aventure de science-fiction fantastique', 'https://covers.openlibrary.org/b/isbn/9780312367541-L.jpg', 0, '2025-06-04 21:02:17'),
(100, 'The Giving Tree', 'Shel Silverstein', '9780060256654', 8.99, 52, 1, 'Un conte intemporel d\'amour inconditionnel', 'https://covers.openlibrary.org/b/isbn/9780060256654-L.jpg', 1, '2025-06-04 21:02:17');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `book_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`id`, `session_id`, `book_id`, `quantity`, `created_at`) VALUES
(8, 'mo6d2d3rvhr96v4d4lf65gaosd', 2, 1, '2025-06-05 23:26:43'),
(9, 'aad50mpkbo6f7oi3t7d321gruv', 2, 1, '2025-06-06 15:30:16'),
(33, 'q3e1g3d263co6q066chjc4c5lp', 75, 1, '2025-06-07 13:57:24'),
(34, 'q3e1g3d263co6q066chjc4c5lp', 99, 1, '2025-06-07 13:58:12'),
(35, 'poohlrg5rac9dplcp7b5buqt56', 1, 1, '2025-06-07 14:00:56'),
(36, '84p4lqog6nl7phskf7kpbjatgh', 1, 1, '2025-06-07 14:01:00'),
(37, '0g37quo7u9usn90lmo6i8koa9c', 2, 1, '2025-06-07 14:01:34'),
(38, '8jp8vve117bnqltqcpbjt90keo', 14, 1, '2025-06-07 14:01:42'),
(39, 'q3e1g3d263co6q066chjc4c5lp', 77, 1, '2025-06-07 14:01:49'),
(40, 'ce7jcc4bn729e1jrol8ohjv99a', 4, 1, '2025-06-07 14:04:42'),
(41, '9fp68fkr2e0jvl5od5d0fqt46k', 2, 1, '2025-06-07 14:05:11'),
(42, 'fgmif4embfkoi0e4299emuuomo', 2, 1, '2025-06-07 14:05:15'),
(43, '0e0c929hmh2qaavb1jgkdhs4of', 4, 1, '2025-06-07 14:24:02'),
(44, '3gd73bhsi4mdbeutfc945p5j10', 4, 1, '2025-06-07 14:24:09'),
(46, 'tgvqpi5mijfkipserekjmks92j', 4, 1, '2025-06-07 14:32:23'),
(47, 'odac0m17k0s4gq9296f0e74jec', 8, 1, '2025-06-07 14:33:00'),
(48, 'e2jvgn31c7ignbvfu60hd3koog', 8, 1, '2025-06-07 14:33:10'),
(53, '3k8g1hn11p6vrvrnasviovgerk', 2, 1, '2025-06-07 15:01:55'),
(54, '3k8g1hn11p6vrvrnasviovgerk', 3, 1, '2025-06-07 15:03:00'),
(76, 'ubosk0i4fj6akr1ukpjuc817dk', 28, 1, '2025-06-08 16:11:15'),
(77, 'ubosk0i4fj6akr1ukpjuc817dk', 3, 1, '2025-06-08 16:11:18'),
(81, 'j01vhdrb9d5pvt96vd8lfcj562', 2, 1, '2025-06-10 10:48:34'),
(84, '90ib7l7i84i1iclpr81omc5bg9', 4, 1, '2025-06-10 11:49:19'),
(85, '90ib7l7i84i1iclpr81omc5bg9', 15, 1, '2025-06-10 11:52:07'),
(86, '90ib7l7i84i1iclpr81omc5bg9', 13, 1, '2025-06-10 11:52:11'),
(87, '90ib7l7i84i1iclpr81omc5bg9', 8, 1, '2025-06-10 11:52:15');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`) VALUES
(1, 'Fiction', 'Novels, short stories, and fictional literature', '2025-06-04 21:02:17'),
(2, 'Non-Fiction', 'Biographies, history, and factual books', '2025-06-04 21:02:17'),
(3, 'Science Fiction', 'Futuristic and speculative fiction', '2025-06-04 21:02:17'),
(4, 'Mystery', 'Crime, detective, and mystery novels', '2025-06-04 21:02:17'),
(5, 'Romance', 'Love stories and romantic fiction', '2025-06-04 21:02:17'),
(6, 'Business', 'Business, entrepreneurship, and management', '2025-06-04 21:02:17'),
(7, 'Technology', 'Programming, computers, and tech books', '2025-06-04 21:02:17'),
(8, 'Self-Help', 'Personal development and improvement', '2025-06-04 21:02:17');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `customer_email` varchar(255) NOT NULL,
  `customer_phone` varchar(20) DEFAULT NULL,
  `shipping_address` text NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','processing','shipped','delivered') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `book_id` int(11) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `book_id` (`book_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=101;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `books`
--
ALTER TABLE `books`
  ADD CONSTRAINT `books_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`);

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
