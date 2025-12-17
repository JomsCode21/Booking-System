<?php
session_start();
require 'connection/db_con.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

if (isset($_GET['action']) && $_GET['action'] == 'new') {
    unset($_SESSION['booking']);
    header("Location: user_booking.php");
    exit();
}

if (!isset($_SESSION['booking'])) { $_SESSION['booking'] = ['step' => 1]; }

$error = null; 
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['step1'])) {
        $_SESSION['booking']['cottage_name'] = $_POST['cottage_name'];
        $_SESSION['booking']['price_day'] = $_POST['price_day'];
        $_SESSION['booking']['price_night'] = $_POST['price_night'];
        $_SESSION['booking']['price_24h'] = $_POST['price_24h'];
        $_SESSION['booking']['image_url'] = $_POST['image_url'];
        $_SESSION['booking']['step'] = 2;
    } 

    elseif (isset($_POST['step2'])) {
        $date = $_POST['date'];
        $type = $_POST['tour_type'];
        $cottage = $_SESSION['booking']['cottage_name'];
        
        // Check availability
        $stmt = $conn->prepare("SELECT COUNT(*) as count FROM bookings WHERE cottage_name = ? AND check_in = ? AND tour_type = ? AND status != 'Cancelled'");
        $stmt->bind_param("sss", $cottage, $date, $type);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['count'] > 0) {
            // SLOT IS TAKEN
            $error = "Sorry! The <strong>$cottage</strong> is already fully booked for a <strong>$type</strong> on " . date('F j, Y', strtotime($date)) . ".";
        } else {
            // SLOT IS FREE - PROCEED
            $_SESSION['booking']['date'] = $date;
            $_SESSION['booking']['tour_type'] = $type; 

            if ($type == 'Day Tour') {
                $_SESSION['booking']['final_price'] = $_SESSION['booking']['price_day'];
            } elseif ($type == 'Night Stay') {
                $_SESSION['booking']['final_price'] = $_SESSION['booking']['price_night'];
            } else {
                $_SESSION['booking']['final_price'] = $_SESSION['booking']['price_24h'];
            }
            
            $_SESSION['booking']['step'] = 3;
        }
    } 
    
    elseif (isset($_POST['step3'])) {
        $_SESSION['booking']['contact'] = $_POST['contact'];
        $_SESSION['booking']['step'] = 4;
    } 

    elseif (isset($_POST['step4'])) {
        $ref = "BK-" . strtoupper(substr(md5(time()), 0, 6));
        $uid = $_SESSION['user_id'];
        
        $stmt = $conn->prepare("INSERT INTO bookings (ref_number, user_id, cottage_name, tour_type, check_in, total_price, status) VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
        $stmt->bind_param("sisssd", $ref, $uid, $_SESSION['booking']['cottage_name'], $_SESSION['booking']['tour_type'], $_SESSION['booking']['date'], $_SESSION['booking']['final_price']);
        
        if($stmt->execute()) {
            $_SESSION['booking']['ref'] = $ref;
            $_SESSION['booking']['step'] = 5;
        } else {
            $error = "System Error: " . $conn->error;
        }
    }
}

$step = $_SESSION['booking']['step'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Stay | Cymae Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>html { scroll-behavior: smooth; }</style>
</head>
<body class="bg-blue-50 font-sans text-gray-800 min-h-screen flex flex-col">

    <nav class="bg-white/90 backdrop-blur-md shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-2">
                    <div class="bg-cyan-500 text-white p-2 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="font-bold text-xl md:text-2xl text-cyan-700 tracking-wide uppercase">Cymae Booking</span>
                </div>
                <div>
                    <div class="flex items-center gap-4">
                        <a href="user_profile.php" class="text-sm font-medium text-gray-600 hover:text-cyan-600 transition flex items-center gap-1">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            My Profile
                        </a>
                        <span class="text-gray-300">|</span>
                        <a href="logout.php" class="text-sm font-medium text-red-500 hover:text-red-700 transition">Log Out</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <?php if ($step < 5): ?>
    <div class="bg-white border-b">
        <div class="max-w-4xl mx-auto py-4 px-4">
            <div class="flex justify-between items-center text-sm font-medium text-gray-400">
                <span class="<?= $step >= 1 ? 'text-cyan-600 font-bold' : '' ?>">1. Select Cottage</span>
                <span class="border-t w-10 md:w-20"></span>
                <span class="<?= $step >= 2 ? 'text-cyan-600 font-bold' : '' ?>">2. Date & Time</span>
                <span class="border-t w-10 md:w-20"></span>
                <span class="<?= $step >= 3 ? 'text-cyan-600 font-bold' : '' ?>">3. Details</span>
                <span class="border-t w-10 md:w-20"></span>
                <span class="<?= $step >= 4 ? 'text-cyan-600 font-bold' : '' ?>">4. Confirm</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="flex-grow container mx-auto px-4 py-8">

        <?php if ($step == 1): ?>
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800">Choose your Cottage</h1>
                <p class="text-gray-500 mt-2">Select the perfect spot for your getaway.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-7xl mx-auto">
                <?php 
                $cottages = $conn->query("SELECT * FROM cottages");
                while($c = $cottages->fetch_assoc()): 
                ?>
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-2xl transition duration-300 border border-gray-100 flex flex-col">
                    <div class="relative h-56">
                        <img src="<?= $c['image_url'] ?>" alt="<?= $c['name'] ?>" class="w-full h-full object-cover">
                        <div class="absolute bottom-0 left-0 bg-gradient-to-t from-black/70 to-transparent w-full p-4">
                            <h3 class="text-white font-bold text-xl"><?= $c['name'] ?></h3>
                            <p class="text-white/80 text-xs"><?= $c['capacity'] ?></p>
                        </div>
                    </div>
                    <div class="p-6 flex-grow">
                        <div class="grid grid-cols-3 gap-2 mb-4 border-b pb-4 text-center">
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold">Day</p>
                                <p class="font-bold text-sm text-cyan-600">₱<?= number_format($c['price_day']) ?></p>
                            </div>
                            <div class="border-x border-gray-100">
                                <p class="text-[10px] text-gray-500 uppercase font-bold">Night</p>
                                <p class="font-bold text-sm text-indigo-600">₱<?= number_format($c['price_night']) ?></p>
                            </div>
                            <div>
                                <p class="text-[10px] text-gray-500 uppercase font-bold">24 Hrs</p>
                                <p class="font-bold text-sm text-purple-600">₱<?= number_format($c['price_24h']) ?></p>
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm italic"><?= $c['description'] ?? 'Relaxing ambiance.' ?></p>
                    </div>
                    <div class="p-6 pt-0 mt-auto">
                        <form method="POST">
                            <input type="hidden" name="cottage_name" value="<?= $c['name'] ?>">
                            <input type="hidden" name="price_day" value="<?= $c['price_day'] ?>">
                            <input type="hidden" name="price_night" value="<?= $c['price_night'] ?>">
                            <input type="hidden" name="price_24h" value="<?= $c['price_24h'] ?>"> <input type="hidden" name="image_url" value="<?= $c['image_url'] ?>">
                            <button type="submit" name="step1" class="w-full py-3 bg-cyan-500 hover:bg-cyan-600 text-white font-bold rounded-lg transition shadow-md">
                                Book This Cottage
                            </button>
                        </form>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>


        <?php if ($step == 2): ?>
            <div class="max-w-xl mx-auto bg-white p-8 rounded-xl shadow-xl border border-gray-100">
                <div class="flex items-center gap-4 mb-6 border-b pb-4">
                    <img src="<?= $_SESSION['booking']['image_url'] ?>" class="w-20 h-20 object-cover rounded-lg shadow-sm">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">You selected:</h2>
                        <p class="text-cyan-600 font-bold text-lg"><?= $_SESSION['booking']['cottage_name'] ?></p>
                    </div>
                </div>

                <?php if ($error): ?>
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6" role="alert">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700"><?= $error ?></p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Check-in Date</label>
                        <input type="date" name="date" required min="<?= date('Y-m-d') ?>" value="<?= isset($_POST['date']) ? $_POST['date'] : '' ?>" class="w-full border-gray-300 border p-3 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Choose Type of Stay</label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="tour_type" value="Day Tour" class="peer sr-only" required <?= (isset($_POST['tour_type']) && $_POST['tour_type'] == 'Day Tour') ? 'checked' : '' ?>>
                                <div class="p-4 border rounded-lg text-center peer-checked:bg-cyan-50 peer-checked:border-cyan-500 peer-checked:text-cyan-700 transition hover:bg-gray-50 h-full flex flex-col justify-center">
                                    <span class="block font-bold">Day Tour</span>
                                    <span class="text-[10px] text-gray-500">8AM - 6PM</span>
                                    <span class="text-xs font-bold mt-1 text-cyan-600">₱<?= number_format($_SESSION['booking']['price_day']) ?></span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="tour_type" value="Night Stay" class="peer sr-only" required <?= (isset($_POST['tour_type']) && $_POST['tour_type'] == 'Night Stay') ? 'checked' : '' ?>>
                                <div class="p-4 border rounded-lg text-center peer-checked:bg-indigo-50 peer-checked:border-indigo-500 peer-checked:text-indigo-700 transition hover:bg-gray-50 h-full flex flex-col justify-center">
                                    <span class="block font-bold">Night Stay</span>
                                    <span class="text-[10px] text-gray-500">7PM - 7AM</span>
                                    <span class="text-xs font-bold mt-1 text-indigo-600">₱<?= number_format($_SESSION['booking']['price_night']) ?></span>
                                </div>
                            </label>

                            <label class="cursor-pointer">
                                <input type="radio" name="tour_type" value="24 Hours" class="peer sr-only" required <?= (isset($_POST['tour_type']) && $_POST['tour_type'] == '24 Hours') ? 'checked' : '' ?>>
                                <div class="p-4 border rounded-lg text-center peer-checked:bg-purple-50 peer-checked:border-purple-500 peer-checked:text-purple-700 transition hover:bg-gray-50 h-full flex flex-col justify-center">
                                    <span class="block font-bold">24 Hours</span>
                                    <span class="text-[10px] text-gray-500">Flexible Check-in</span>
                                    <span class="text-xs font-bold mt-1 text-purple-600">₱<?= number_format($_SESSION['booking']['price_24h']) ?></span>
                                </div>
                            </label>

                        </div>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <a href="?action=new" class="w-1/3 py-3 text-center border text-gray-600 rounded-lg hover:bg-gray-50 font-medium">Cancel</a>
                        <button type="submit" name="step2" class="w-2/3 py-3 bg-cyan-600 text-white rounded-lg font-bold hover:bg-cyan-700 shadow-md">Check Availability</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>


        <?php if ($step == 3): ?>
            <div class="max-w-md mx-auto bg-white p-8 rounded-xl shadow-xl border border-gray-100">
                <h2 class="text-2xl font-bold mb-2 text-gray-800">Contact Details</h2>
                <form method="POST">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Mobile Number</label>
                    <input type="text" name="contact" placeholder="09XX XXX XXXX" required class="w-full border border-gray-300 p-3 rounded-lg mb-6 focus:ring-2 focus:ring-cyan-500 outline-none">
                    
                    <div class="flex gap-3">
                        <a href="?action=new" class="w-1/3 py-3 text-center border text-gray-600 rounded-lg hover:bg-gray-50 font-medium">Cancel</a>
                        <button type="submit" name="step3" class="w-2/3 bg-cyan-600 text-white p-3 rounded-lg font-bold hover:bg-cyan-700 shadow-md">Review Booking</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>


        <?php if ($step == 4): ?>
            <div class="max-w-lg mx-auto bg-white rounded-xl shadow-2xl overflow-hidden">
                <div class="bg-gray-900 p-6 text-white text-center">
                    <h2 class="text-xl font-bold uppercase tracking-wider">Booking Summary</h2>
                </div>
                <div class="p-8 space-y-4">
                    <div class="flex justify-between border-b border-dashed pb-3">
                        <span class="text-gray-500">Cottage</span>
                        <span class="font-bold text-gray-800"><?= $_SESSION['booking']['cottage_name'] ?></span>
                    </div>
                    <div class="flex justify-between border-b border-dashed pb-3">
                        <span class="text-gray-500">Date</span>
                        <span class="font-bold text-gray-800"><?= date('F j, Y', strtotime($_SESSION['booking']['date'])) ?></span>
                    </div>
                    <div class="flex justify-between border-b border-dashed pb-3">
                        <span class="text-gray-500">Type</span>
                        <span class="font-bold text-gray-800"><?= $_SESSION['booking']['tour_type'] ?></span>
                    </div>
                    <div class="flex justify-between border-b border-dashed pb-3">
                        <span class="text-gray-500">Contact</span>
                        <span class="font-bold text-gray-800"><?= $_SESSION['booking']['contact'] ?></span>
                    </div>
                    <div class="bg-cyan-50 p-4 rounded-lg flex justify-between items-center mt-4">
                        <span class="font-bold text-cyan-800">Total Amount</span>
                        <span class="font-extrabold text-2xl text-cyan-700">₱<?= number_format($_SESSION['booking']['final_price']) ?></span>
                    </div>
                </div>
                <div class="p-6 bg-gray-50 border-t flex gap-4">
                    <a href="?action=new" class="w-1/2 py-3 text-center border bg-white text-gray-600 rounded-lg hover:bg-gray-100 font-bold">Cancel</a>
                    <form method="POST" class="w-1/2">
                        <button type="submit" name="step4" class="w-full bg-green-500 text-white py-3 rounded-lg font-bold shadow hover:bg-green-600">Confirm Reservation</button>
                    </form>
                </div>
            </div>
        <?php endif; ?>


        <?php if ($step == 5): ?>
            <div class="max-w-md mx-auto bg-white p-10 rounded-xl shadow-2xl text-center mt-10">
                <div class="w-20 h-20 bg-green-100 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                </div>
                <h2 class="text-2xl font-extrabold text-gray-800 mb-2">Booking Confirmed!</h2>
                <div class="bg-gray-100 p-4 rounded-lg mb-6 border border-dashed border-gray-300">
                    <p class="text-xs text-gray-500 uppercase">Reference Number</p>
                    <p class="text-2xl font-mono font-bold text-gray-900 tracking-widest"><?= $_SESSION['booking']['ref'] ?></p>
                </div>
                <a href="?action=new" class="inline-block w-full py-3 bg-cyan-600 text-white font-bold rounded-lg hover:bg-cyan-700">Make Another Booking</a>
            </div>
        <?php endif; ?>

    </div>
</body>
</html>