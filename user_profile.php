<?php
session_start();
require 'connection/db_con.php';

if (!isset($_SESSION['user_id'])) { 
    header("Location: login.php"); 
    exit(); 
}

$uid = $_SESSION['user_id'];
$message = "";
$msg_type = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['update_info'])) {
        $fullname = $_POST['fullname'];
        $contact  = $_POST['contact']; 
        
        $stmt = $conn->prepare("UPDATE users SET name = ?, contact_number = ? WHERE id = ?");
        $stmt->bind_param("ssi", $fullname, $contact, $uid);
        
        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
            $msg_type = "success";
        } else {
            $message = "Error updating profile.";
            $msg_type = "error";
        }
    }

    // Change Password
    if (isset($_POST['change_pass'])) {
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];

        if ($new_pass === $confirm_pass) {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_pass, $uid);
            
            if ($stmt->execute()) {
                $message = "Password changed successfully!";
                $msg_type = "success";
            }
        } else {
            $message = "Passwords do not match.";
            $msg_type = "error";
        }
    }
}

$user_query = $conn->prepare("SELECT * FROM users WHERE id = ?");
$user_query->bind_param("i", $uid);
$user_query->execute();
$user = $user_query->get_result()->fetch_assoc();

$history_query = $conn->prepare("SELECT * FROM bookings WHERE user_id = ? ORDER BY id DESC");
$history_query->bind_param("i", $uid);
$history_query->execute();
$history = $history_query->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Cymae Resort</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-blue-50 font-sans text-gray-800 min-h-screen">

    <nav class="bg-white/90 backdrop-blur-md shadow-md sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <a href="user_booking.php" class="flex items-center space-x-2">
                    <div class="bg-cyan-500 text-white p-2 rounded-full">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    </div>
                    <span class="font-bold text-lg text-cyan-700 uppercase">Back to Booking</span>
                </a>
                <div>
                    <a href="logout.php" class="text-sm font-medium text-red-500 hover:text-red-700 transition">Log Out</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8 grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <div class="lg:col-span-1 space-y-6">
            
            <?php if ($message): ?>
                <div class="p-4 rounded-lg <?= $msg_type == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                    <?= $message ?>
                </div>
            <?php endif; ?>

            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Account Settings
                </h3>
                
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Full Name</label>
                        <input type="text" name="fullname" value="<?= htmlspecialchars($user['name'] ?? '') ?>" class="w-full border-b border-gray-300 py-2 focus:border-cyan-500 outline-none transition bg-transparent" placeholder="Your Name">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Contact Number</label>
                        <input type="number" name="contact" value="<?= htmlspecialchars($user['contact_number'] ?? '') ?>" class="w-full border-b border-gray-300 py-2 focus:border-cyan-500 outline-none transition bg-transparent" placeholder="09XX...">
                    </div>
                    <button type="submit" name="update_info" class="w-full bg-gray-800 text-white py-2 rounded-lg text-sm font-bold hover:bg-gray-900 transition">Update Info</button>
                </form>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-lg border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-cyan-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Security
                </h3>
                <form method="POST" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">New Password</label>
                        <input type="password" name="new_password" required class="w-full border-b border-gray-300 py-2 focus:border-cyan-500 outline-none transition bg-transparent">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase">Confirm Password</label>
                        <input type="password" name="confirm_password" required class="w-full border-b border-gray-300 py-2 focus:border-cyan-500 outline-none transition bg-transparent">
                    </div>
                    <button type="submit" name="change_pass" class="w-full border border-red-500 text-red-500 py-2 rounded-lg text-sm font-bold hover:bg-red-50 transition">Change Password</button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h2 class="text-xl font-bold text-gray-800">Booking History</h2>
                    <span class="bg-cyan-100 text-cyan-800 text-xs font-bold px-2 py-1 rounded-full"><?= $history->num_rows ?> Records</span>
                </div>
                
                <div class="overflow-x-auto">
                    <?php if ($history->num_rows > 0): ?>
                    <table class="w-full text-left text-sm text-gray-600">
                        <thead class="bg-gray-100 text-gray-700 uppercase font-bold text-xs">
                            <tr>
                                <th class="p-4">Ref #</th>
                                <th class="p-4">Cottage</th>
                                <th class="p-4">Details</th>
                                <th class="p-4">Total</th>
                                <th class="p-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php while($row = $history->fetch_assoc()): 
                                // Color logic for status
                                $status_color = 'bg-yellow-100 text-yellow-800'; // Default Pending
                                if($row['status'] == 'Confirmed' || $row['status'] == 'Paid') $status_color = 'bg-green-100 text-green-800';
                                if($row['status'] == 'Cancelled') $status_color = 'bg-red-100 text-red-800';
                            ?>
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4 font-mono font-bold text-gray-800"><?= $row['ref_number'] ?></td>
                                <td class="p-4 font-medium"><?= $row['cottage_name'] ?></td>
                                <td class="p-4">
                                    <div class="flex flex-col">
                                        <span class="font-bold text-gray-800"><?= date('M j, Y', strtotime($row['check_in'])) ?></span>
                                        <span class="text-xs"><?= $row['tour_type'] ?></span>
                                    </div>
                                </td>
                                <td class="p-4 font-bold text-cyan-600">₱<?= number_format($row['total_price']) ?></td>
                                <td class="p-4">
                                    <span class="px-3 py-1 rounded-full text-xs font-bold <?= $status_color ?>">
                                        <?= $row['status'] ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                        <div class="p-10 text-center text-gray-400">
                            <svg class="w-12 h-12 mx-auto mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            <p>No bookings found.</p>
                            <a href="user_booking.php" class="text-cyan-600 font-bold hover:underline mt-2 inline-block">Book a cottage now</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</body>
</html>