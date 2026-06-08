<?php
include 'config.php';
// Fetch upcoming events (ordered by date)
$events = $conn->query("SELECT * FROM events WHERE event_date >= CURDATE() ORDER BY event_date ASC");
?>

<section id="events" style="padding: 60px 10%; background: #f9f9f9;">
    <h2 style="text-align: center; color: #0b1c3d; margin-bottom: 40px; font-size: 32px;">Upcoming Events</h2>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
        <?php if($events->num_rows > 0): ?>
            <?php while($row = $events->fetch_assoc()): ?>
                <div style="background: white; padding: 25px; border-radius: 10px; border-left: 5px solid #d60000; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">
                    <p style="color: #d60000; font-weight: bold; font-size: 14px; margin-bottom: 10px;">
                        <i class="fas fa-calendar"></i> <?= date('F d, Y', strtotime($row['event_date'])); ?>
                    </p>
                    <h3 style="color: #0b1c3d; margin-bottom: 10px;"><?= htmlspecialchars($row['title']); ?></h3>
                    <p style="color: #666; font-size: 14px;">
                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['location']); ?>
                    </p>
                    <a href="#contact" style="display: inline-block; margin-top: 15px; color: #d60000; text-decoration: none; font-weight: bold;">RSVP Now →</a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p style="text-align: center; grid-column: 1 / -1;">No upcoming events scheduled. Stay tuned!</p>
        <?php endif; ?>
    </div>
</section>