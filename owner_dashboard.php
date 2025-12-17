<?php
session_start();
require 'connection/db_con.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'owner') {
    header("Location: login.php");
    exit();
}

if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

if (isset($_POST['add_cottage'])) {
    $name = $_POST['name'];
    $price_day = $_POST['price_day'];
    $price_night = $_POST['price_night'];
    $price_24h = $_POST['price_24h'];
    $capacity = $_POST['capacity'];
    
    $image_path = ""; 
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $filename = time() . "_" . basename($_FILES['image']['name']);
        $target_file = "uploads/" . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("INSERT INTO cottages (name, price_day, price_night, price_24h, capacity, image_url) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sdddss", $name, $price_day, $price_night, $price_24h, $capacity, $image_path);
    $stmt->execute();
    header("Location: owner_dashboard.php");
    exit();
}

if (isset($_POST['update_cottage'])) {
    $id = $_POST['cottage_id'];
    $name = $_POST['name'];
    $price_day = $_POST['price_day'];
    $price_night = $_POST['price_night'];
    $price_24h = $_POST['price_24h'];
    $capacity = $_POST['capacity'];
    $image_path = $_POST['old_image']; 

    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $filename = time() . "_" . basename($_FILES['image']['name']);
        $target_file = "uploads/" . $filename;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }

    $stmt = $conn->prepare("UPDATE cottages SET name=?, price_day=?, price_night=?, price_24h=?, capacity=?, image_url=? WHERE id=?");
    $stmt->bind_param("sdddssi", $name, $price_day, $price_night, $price_24h, $capacity, $image_path, $id);
    $stmt->execute();
    header("Location: owner_dashboard.php");
    exit();
}

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM cottages WHERE id = " . $_GET['delete']);
    header("Location: owner_dashboard.php");
    exit();
}

if (isset($_POST['update_booking_status'])) {
    $booking_id = $_POST['booking_id'];
    $new_status = $_POST['update_booking_status']; 
    
    $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=?");
    $stmt->bind_param("si", $new_status, $booking_id);
    $stmt->execute();
    
    header("Location: owner_dashboard.php");
    exit();
}

$edit_mode = false;
$edit_data = ['name'=>'', 'price_day'=>'', 'price_night'=>'', 'price_24h'=>'', 'capacity'=>'', 'image_url'=>'', 'id'=>''];

if (isset($_GET['edit'])) {
    $edit_mode = true;
    $result = $conn->query("SELECT * FROM cottages WHERE id = " . $_GET['edit']);
    $edit_data = $result->fetch_assoc();
}

$bookings = $conn->query("SELECT bookings.*, users.name as guest_name FROM bookings JOIN users ON bookings.user_id = users.id ORDER BY bookings.id DESC");
$cottages = $conn->query("SELECT * FROM cottages");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Owner Dashboard | Cymae Resort</title>
    <link rel="shortcut icon" href="images/logo.png" type="image/x-icon">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        html { scroll-behavior: smooth; }
    </style>
</head>
<body class="font-sans text-gray-800 bg-blue-50 pt-24 pb-20">

    <nav class="bg-white/90 backdrop-blur-md shadow-md fixed w-full top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="index.php" class="flex items-center space-x-2">
                    <div class="bg-cyan-500 text-white p-2 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="font-bold text-xl md:text-2xl text-cyan-700 tracking-wide uppercase">Cymae Admin</span>
                </a>

                <div class="flex items-center space-x-4">
                    <span class="hidden md:inline text-gray-500 text-sm">Welcome, Owner</span>
                    <a href="logout.php" class="px-5 py-2 bg-red-500 hover:bg-red-600 text-white font-medium rounded-full shadow-md transition transform hover:scale-105 text-sm">
                        Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 md:px-6">
        
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard Overview</h1>
            <p class="text-gray-500 mt-1">Manage your cottages and view guest reservations.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
            
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 p-6 h-fit sticky top-28">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="font-bold text-xl text-cyan-700">
                        <?= $edit_mode ? 'Edit Cottage' : 'Add New Cottage' ?>
                    </h3>
                    <?php if($edit_mode): ?>
                        <span class="text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full font-bold">Editing Mode</span>
                    <?php endif; ?>
                </div>

                <form method="POST" enctype="multipart/form-data" class="space-y-4">
                    <input type="hidden" name="cottage_id" value="<?= $edit_data['id'] ?>">
                    <input type="hidden" name="old_image" value="<?= $edit_data['image_url'] ?>">
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cottage Name</label>
                        <input type="text" name="name" value="<?= $edit_data['name'] ?>" placeholder="e.g. Family Villa" required 
                               class="w-full border-gray-300 border p-3 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition bg-gray-50">
                    </div>

                    <div class="grid grid-cols-3 gap-2">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Day (₱)</label>
                            <input type="number" step="0.01" name="price_day" value="<?= $edit_data['price_day'] ?>" placeholder="0.00" required 
                                   class="w-full border-gray-300 border p-2 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition bg-gray-50 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1">Night (₱)</label>
                            <input type="number" step="0.01" name="price_night" value="<?= $edit_data['price_night'] ?>" placeholder="0.00" required 
                                   class="w-full border-gray-300 border p-2 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition bg-gray-50 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-purple-600 mb-1">24 Hrs (₱)</label>
                            <input type="number" step="0.01" name="price_24h" value="<?= $edit_data['price_24h'] ?>" placeholder="0.00" required 
                                   class="w-full border-purple-300 border p-2 rounded-lg focus:ring-2 focus:ring-purple-500 outline-none transition bg-purple-50 text-sm">
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Capacity</label>
                        <input type="text" name="capacity" value="<?= $edit_data['capacity'] ?>" placeholder="e.g. 10-15 Pax" required 
                               class="w-full border-gray-300 border p-3 rounded-lg focus:ring-2 focus:ring-cyan-500 outline-none transition bg-gray-50">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-600 mb-1">Cottage Image</label>
                        <?php if($edit_mode && !empty($edit_data['image_url'])): ?>
                            <div class="mb-2 flex items-center gap-2 text-xs text-gray-500">
                                <img src="<?= $edit_data['image_url'] ?>" class="w-10 h-10 object-cover rounded border">
                                <span>Current Image</span>
                            </div>
                        <?php endif; ?>

                        <input type="file" name="image" accept="image/*" 
                               class="w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-cyan-50 file:text-cyan-700
                                      hover:file:bg-cyan-100 border border-gray-300 rounded-lg cursor-pointer" 
                               <?= $edit_mode ? '' : 'required' ?>>
                        <p class="text-xs text-gray-400 mt-1">Formats: JPG, PNG, JPEG</p>
                    </div>
                    
                    <div class="pt-2">
                        <?php if($edit_mode): ?>
                            <div class="flex gap-3">
                                <button type="submit" name="update_cottage" class="flex-1 bg-yellow-400 hover:bg-yellow-500 text-gray-900 font-bold py-3 rounded-lg shadow transition">Save Changes</button>
                                <a href="owner_dashboard.php" class="px-4 py-3 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">Cancel</a>
                            </div>
                        <?php else: ?>
                            <button type="submit" name="add_cottage" class="w-full bg-cyan-500 hover:bg-cyan-600 text-white font-bold py-3 rounded-lg shadow-md transition transform hover:-translate-y-1">
                                + Add Cottage
                            </button>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <div class="lg:col-span-2 space-y-6">
                <h3 class="font-bold text-xl text-gray-800 mb-4 border-l-4 border-cyan-500 pl-3">Current Cottages</h3>
                
                <?php if($cottages->num_rows > 0): ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <?php while($c = $cottages->fetch_assoc()): ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-xl transition duration-300 border border-gray-100 flex flex-col">
                            <div class="relative h-48">
                                <img src="<?= $c['image_url'] ?>" alt="<?= $c['name'] ?>" class="w-full h-full object-cover">
                                <div class="absolute top-2 right-2 flex gap-2">
                                    <a href="?edit=<?= $c['id'] ?>" class="bg-white/90 p-2 rounded-full text-blue-600 hover:text-blue-800 shadow-sm backdrop-blur-sm transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" /></svg>
                                    </a>
                                    <a href="?delete=<?= $c['id'] ?>" onclick="return confirm('Are you sure you want to delete this cottage?')" class="bg-white/90 p-2 rounded-full text-red-500 hover:text-red-700 shadow-sm backdrop-blur-sm transition">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" /></svg>
                                    </a>
                                </div>
                            </div>
                            <div class="p-5 flex-1 flex flex-col">
                                <h4 class="font-bold text-lg text-gray-800"><?= $c['name'] ?></h4>
                                <p class="text-sm text-gray-500 mb-3 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    <?= $c['capacity'] ?>
                                </p>
                                <div class="mt-auto space-y-1">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Day Rate:</span>
                                        <span class="font-bold text-cyan-600">₱<?= number_format($c['price_day']) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Night Rate:</span>
                                        <span class="font-bold text-indigo-600">₱<?= number_format($c['price_night']) ?></span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">24 Hrs:</span>
                                        <span class="font-bold text-purple-600">₱<?= number_format($c['price_24h']) ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="bg-white rounded-xl shadow p-8 text-center text-gray-500">
                        No cottages found. Add one to get started.
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <h2 class="text-2xl font-bold mb-6 text-gray-800 flex items-center gap-2">
            <svg class="w-6 h-6 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
            Recent Reservations
        </h2>
        
        <div class="bg-white shadow-lg rounded-xl overflow-hidden border border-gray-100">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-cyan-50 text-cyan-900 border-b border-cyan-100">
                        <tr>
                            <th class="p-4 font-bold">Ref No.</th>
                            <th class="p-4 font-bold">Guest Name</th>
                            <th class="p-4 font-bold">Cottage</th>
                            <th class="p-4 font-bold">Type</th>
                            <th class="p-4 font-bold">Date</th>
                            <th class="p-4 font-bold">Total</th>
                            <th class="p-4 font-bold">Status</th> <th class="p-4 font-bold text-center">Actions</th> </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php while($b = $bookings->fetch_assoc()): ?>
                        <tr class="hover:bg-blue-50/50 transition duration-150">
                            <td class="p-4 font-mono text-cyan-600 font-medium">#<?= $b['ref_number'] ?></td>
                            <td class="p-4 font-medium text-gray-700"><?= $b['guest_name'] ?></td>
                            <td class="p-4 text-gray-600"><?= $b['cottage_name'] ?></td>
                            <td class="p-4">
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                    <?= $b['tour_type'] ?>
                                </span>
                            </td>
                            <td class="p-4 text-gray-600">
                                <?= date("M d, Y", strtotime($b['check_in'])) ?>
                            </td>
                            <td class="p-4 font-bold text-gray-800">₱<?= number_format($b['total_price']) ?></td>

                            <td class="p-4">
                                <?php 
                                    $status = strtolower($b['status']); 
                                    $statusColor = 'bg-gray-100 text-gray-600'; // Default Pending

                                    if($status == 'confirmed') {
                                        $statusColor = 'bg-green-100 text-green-700';
                                    } elseif($status == 'cancelled') {
                                        $statusColor = 'bg-red-100 text-red-700';
                                    } elseif($status == 'pending') {
                                        $statusColor = 'bg-yellow-100 text-yellow-700';
                                    }
                                ?>
                                <span class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide <?= $statusColor ?>">
                                    <?= $b['status'] ?>
                                </span>
                            </td>
                                
                            <td class="p-4 text-center">
                                <?php if(strtolower($b['status']) == 'pending'): ?>
                                    <form method="POST" class="flex items-center justify-center gap-2">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">

                                        <button type="submit" name="update_booking_status" value="Confirmed" 
                                                class="p-2 bg-green-500 hover:bg-green-600 text-white rounded-lg shadow-sm transition"
                                                title="Confirm Reservation" onclick="return confirm('Are you sure you want to CONFIRM this reservation?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        </button>
                                
                                        <button type="submit" name="update_booking_status" value="Cancelled"
                                                class="p-2 bg-red-500 hover:bg-red-600 text-white rounded-lg shadow-sm transition"
                                                title="Cancel Reservation" onclick="return confirm('Are you sure you want to CANCEL this reservation?')">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                        </button>
                                    </form>
                                <?php else: ?>
                                    <span class="text-xs text-gray-400 italic">Action taken</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    <footer class="mt-20 text-center text-gray-400 text-sm">
        &copy; <?php echo date("Y"); ?> Cymae Beach Resort. Admin Panel.
    </footer>

</body>
</html>