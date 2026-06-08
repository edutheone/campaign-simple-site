<?php
include 'config.php';

/* GET TOTAL SUPPORTERS FROM volunteers TABLE */
$sql = "SELECT COUNT(*) AS total_supporters FROM volunteers";
$result = $conn->query($sql);

$total_supporters = 0;

if ($result && $row = $result->fetch_assoc()) {
    $total_supporters = $row['total_supporters'];
}

#getting ward suppoters
$wardLabels = [];
$wardCounts = [];

$chart_sql = "
    SELECT ward, COUNT(*) AS supporters
    FROM volunteers
    GROUP BY ward
    ORDER BY supporters DESC
";

$chart_result = $conn->query($chart_sql);

if ($chart_result && $chart_result->num_rows > 0) {
    while ($row = $chart_result->fetch_assoc()) {
        $wardLabels[] = $row['ward'];
        $wardCounts[] = $row['supporters'];
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hon. Kennedy Nyamwanda | Senator Nyamira 2027</title>
    <!-- Google Fonts & FontAwesome Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary: #0B3D91;      /* Patriotic Deep Blue */
            --secondary: #D91B23;    /* Campaign Accent Red */
            --accent: #FFD700;       /* Leadership Gold */
            --dark: #1E293B;         /* Dark Slate for text */
            --light: #F8FAFC;        /* Off-white background */
            --white: #FFFFFF;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--light);
            color: var(--dark);
            line-height: 1.6;
        }

        /* NAVBAR */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px 8%;
            background: var(--white);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        .brand {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--primary);
            letter-spacing: -0.5px;
        }
        
        .brand span {
            color: var(--secondary);
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 25px;
        }

        .nav-links a {
            color: var(--dark);
            text-decoration: none;
            font-weight: 600;
            font-size: 0.95rem;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: var(--secondary);
        }

        .nav-links .btn-nav {
            background: var(--primary);
            color: var(--white);
            padding: 10px 20px;
            border-radius: 5px;
            transition: background 0.3s;
        }

        .nav-links .btn-nav:hover {
            background: var(--secondary);
            color: var(--white);
        }

        /* HERO SECTION */
        .hero {
            position: relative;
            background: linear-gradient(135deg, rgba(11,61,145,0.95) 40%, rgba(217,27,35,0.9) 100%), url('campaign-bg.jpg') no-repeat center center/cover;
            color: var(--white);
            padding: 100px 8%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 50px;
            overflow: hidden;
        }

        .hero-content {
            flex: 1;
            max-width: 650px;
        }

        .tagline-badge {
            display: inline-block;
            background: rgba(255, 255, 255, 0.15);
            padding: 6px 15px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.25);
        }

        .hero-content h1 {
            font-size: 3.5rem;
            font-weight: 800;
            line-height: 1.1;
            margin-bottom: 15px;
            letter-spacing: -1px;
        }

        .hero-content h1 span {
            color: var(--accent);
        }

        .hero-content p {
            font-size: 1.2rem;
            margin-bottom: 35px;
            opacity: 0.9;
            font-weight: 300;
        }

        .hero-image-area {
            flex: 1;
            display: flex;
            justify-content: center;
            position: relative;
        }

        .profile-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 20px;
            border-radius: 20px;
            border: 1px solid rgba(255,255,255,0.2);
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
            text-align: center;
        }

        .profile-card img {
            width: 280px;
            height: 280px;
            border-radius: 15px;
            object-fit: cover;
            border: 4px solid var(--white);
            margin-bottom: 15px;
        }

        .profile-card .status {
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        /* BUTTONS */
        .btn-group {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            font-weight: 700;
            font-size: 1rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-action { background: var(--accent); color: var(--dark); }
        .btn-action:hover { background: var(--white); transform: translateY(-2px); }

        .btn-outline { border: 2px solid var(--white); color: var(--white); background: transparent; }
        .btn-outline:hover { background: var(--white); color: var(--primary); transform: translateY(-2px); }

        /* LIVE COUNTER METRIC */
        .counter-banner {
            background: var(--white);
            padding: 40px 8%;
            box-shadow: 0 10px 30px rgba(0,0,0,0.03);
            margin-top: -30px;
            position: relative;
            z-index: 10;
            border-radius: 12px;
            max-width: 84%;
            margin-left: auto;
            margin-right: auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
        }

        .counter-text h2 {
            font-size: 1.8rem;
            color: var(--primary);
            font-weight: 800;
        }

        .counter-text p {
            color: #64748B;
        }

        .counter-number {
            display: flex;
            align-items: center;
            gap: 15px;
            background: #F1F5F9;
            padding: 15px 35px;
            border-radius: 10px;
            border-left: 5px solid var(--secondary);
        }

        .counter-number h1 {
            font-size: 3rem;
            color: var(--dark);
            font-weight: 800;
        }

        /* MISSION SECTION */
        .section-intro {
            text-align: center;
            max-width: 700px;
            margin: 80px auto 40px auto;
            padding: 0 20px;
        }

        .section-intro h2 {
            font-size: 2.5rem;
            color: var(--primary);
            font-weight: 800;
            margin-bottom: 15px;
        }

        .section-intro p {
            font-size: 1.1rem;
            color: #475569;
        }

        /* AGENDA GRID */
        .agenda-container {
            padding: 0 8% 80px 8%;
        }

        .agenda-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
        }

        .agenda-card {
            background: var(--white);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.02);
            border-top: 4px solid var(--primary);
            transition: all 0.3s;
        }

        .agenda-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 30px rgba(0,0,0,0.08);
            border-top-color: var(--secondary);
        }

        .icon-box {
            width: 60px;
            height: 60px;
            background: #EFF6FF;
            color: var(--primary);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 25px;
        }

        .agenda-card:hover .icon-box {
            background: #FEF2F2;
            color: var(--secondary);
        }

        .agenda-card h3 {
            font-size: 1.3rem;
            color: var(--dark);
            margin-bottom: 12px;
            font-weight: 700;
        }

        .agenda-card p {
            color: #64748B;
            font-size: 0.95rem;
        }

        /* CONTACT & FOOTER */
        .footer-cta {
            background: var(--primary);
            color: var(--white);
            padding: 80px 8% 40px 8%;
            text-align: center;
        }

        .cta-content h2 {
            font-size: 2.5rem;
            font-weight: 800;
            margin-bottom: 15px;
        }

        .cta-content p {
            font-size: 1.1rem;
            margin-bottom: 35px;
            opacity: 0.9;
            max-width: 600px;
            margin-left: auto;
            margin-right: auto;
        }

        .contact-info-grid {
            display: flex;
            justify-content: center;
            gap: 40px;
            margin-top: 40px;
            flex-wrap: wrap;
            border-top: 1px solid rgba(255,255,255,0.1);
            padding-top: 40px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 15px;
            font-size: 1.1rem;
        }

        .contact-item i {
            color: var(--accent);
            font-size: 1.3rem;
        }

        .copyright {
            background: #062459;
            color: rgba(255,255,255,0.6);
            text-align: center;
            padding: 25px;
            font-size: 0.9rem;
            border-top: 1px solid rgba(255,255,255,0.05);
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 992px) {
            .hero { flex-direction: column; text-align: center; padding-top: 60px; }
            .hero-content h1 { font-size: 2.8rem; }
            .btn-group { justify-content: center; }
            .counter-banner { flex-direction: column; text-align: center; max-width: 92%; }
            .counter-number { width: 100%; justify-content: center; }
        }

        @media (max-width: 600px) {
            .navbar { padding: 15px 5%; }
            .nav-links a:not(.btn-nav) { display: none; } /* Simplifies mobile nav */
            .hero-content h1 { font-size: 2.2rem; }
            .profile-card img { width: 220px; height: 220px; }
        }
        /*ward graph */
        .ward-chart-section{
    padding:80px 8%;
}

.chart-container{
    background:#fff;
    padding:30px;
    border-radius:15px;
    box-shadow:0 5px 25px rgba(0,0,0,0.05);
    height:500px;
}

#wardChart{
    width:100% !important;
    height:100% !important;
}
    </style>
</head>

<body>

<!-- NAVBAR -->
<div class="navbar">
    <div class="brand">HON. K. <span>NYAMWANDA</span></div>
    <div class="nav-links">
        <a href="events.php">events</a>
        <a href="#agenda">Our Agenda</a>
        <a href="#contact">Contact</a>
        <a href="login.php">Login</a>
        <a href="register.php" class="btn-nav">Join Movement</a>
    </div>
</div>

<!-- HERO SECTION -->
<div class="hero">
    <div class="hero-content">
        <span class="tagline-badge"><i class="fa-solid : true fa-flag"></i> Nyamira County 2027</span>
        <h1>Uongozi Mpya, <span>Jibu Ni Nyamwanda!</span></h1>
        <p>Wamama, vijana, na wazee wameamua. It's time for progressive, inclusive, and transparent leadership dedicated to driving real economic growth and structural development across Nyamira County. Kijana ya nyumbani amerejea kuleta mabadiliko.</p>

        <div class="btn-group">
            <a href="register.php" class="btn btn-action"><i class="fa-solid fa-user-plus"></i> Register as a supporter</a>
            <a href="#agenda" class="btn btn-outline">Explore Our Vision</a>
        </div>
    </div>

    <div class="hero-image-area">
        <div class="profile-card">
            <img src="images/ken.jpeg" alt="Hon. Kennedy Nyamwanda">
            <div class="status">Candidate for Senator 2027</div>
        </div>
    </div>
</div>

<!-- LIVE SUPPORTER METRIC BANNER -->
<div class="counter-banner">
    <div class="counter-text">
        <h2>The People's Movement is Growing</h2>
        <p>Join thousands of local voices standing together for structural reform and community empowerment.</p>
    </div>
    <div class="counter-number">
        <h1><?php echo number_format($total_supporters); ?></h1>
        <p><strong>Registered<br>Supporters</strong></p>
    </div>
</div>

<!-- AGENDA SECTION INTRO -->
<div class="section-intro" id="agenda">
    <h2>The 6 Pillars of Our Manifesto</h2>
    <p>A strategic development plan explicitly mapped out to target economic transformation, wealth creation, and standard-of-living upgrades across all sub-counties.</p>
</div>

<!-- AGENDA GRID -->
<div class="agenda-container">
    <div class="agenda-grid">

        <div class="agenda-card">
            <div class="icon-box"><i class="fa-solid fa-graduation-cap"></i></div>
            <h3>Education Reform</h3>
            <p>Expanding structural resources for vocational centers, ensuring transparent distribution of county bursaries, and building digital hubs to empower students.</p>
        </div>

        <div class="agenda-card">
            <div class="icon-box"><i class="fa-solid fa-heart-pulse"></i></div>
            <h3>Accessible Healthcare</h3>
            <p>Upgrading sub-county clinics with critical medical supplies and improving local healthcare frameworks to provide reliable services to our families.</p>
        </div>

        <div class="agenda-card">
            <div class="icon-box"><i class="fa-solid fa-briefcase"></i></div>
            <h3>Youth Empowerment</h3>
            <p>Fostering job creation through talent incubation, technology bootcamps, and providing enterprise grants to support innovation and young startups.</p>
        </div>

        <div class="agenda-card">
            <div class="icon-box"><i class="fa-solid fa-tractor"></i></div>
            <h3>Agricultural Value Addition</h3>
            <p>Equipping local farmers with direct market linkages, affordable inputs, and modern processing facilities to maximize tea, coffee, and banana yields.</p>
        </div>

        <div class="agenda-card">
            <div class="icon-box"><i class="fa-solid fa-road"></i></div>
            <h3>Infrastructure & Water</h3>
            <p>Advocating for the structural mapping of accessible feeder roads to ease transport, alongside sustainable clean water reticulation projects.</p>
        </div>

        <div class="agenda-card">
            <div class="icon-box"><i class="fa-solid fa-chart-line"></i></div>
            <h3>SME Business Growth</h3>
            <p>Lowering trade barriers, establishing clean market stalls, and setting up flexible local credit frameworks to boost micro-enterprises for local traders.</p>
        </div>

    </div>
</div>
<!-- chat-->
 <!-- SUPPORTERS BY WARD CHART -->
<section class="ward-chart-section">

    <div class="section-intro">
        <h2>Supporters by Ward</h2>
        <p>Live supporter registration statistics across all wards.</p>
    </div>

    <div class="chart-card">
        <canvas id="wardChart"></canvas>
    </div>

</section>

<!-- CONTACT SECTION & CALL TO ACTION -->
<div class="footer-cta" id="contact">
    <div class="cta-content">
        <h2>Be Part of the Historic Change</h2>
        <p>Your voice matters. Reach out to the secretariat to submit ideas, host a grassroots town hall meeting, or become a point-of-contact in your ward.</p>
        <a href="login.php" onclick="alert('login first ..')" class="btn btn-action">click here to send me sms</a>
    </div>

    <div class="contact-info-grid">
        <div class="contact-item">
            <i class="fa-solid fa-envelope"></i>
            <span>info@nyamwanda.com</span>
        </div>
        <div class="contact-item">
            <i class="fa-solid fa-phone"></i>
            <span>+254 700 000 000</span>
        </div>
        <div class="contact-item">
            <i class="fa-solid fa-location-dot"></i>
            <span>Nyamira County, Kenya</span>
        </div>
    </div>
</div>

<!-- FOOTER -->
<div class="copyright">
    &copy; 2026 Hon. Kennedy Nyamwanda Campaign Secretariat. All Rights Reserved.
</div>
<script>

const wardLabels = <?php echo json_encode($wardLabels); ?>;
const wardCounts = <?php echo json_encode($wardCounts); ?>;

const ctx = document.getElementById('wardChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: wardLabels,
        datasets: [{
            label: 'Registered Supporters',
            data: wardCounts,
            backgroundColor: '#0B3D91',
            borderColor: '#D91B23',
            borderWidth: 2,
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,

        plugins: {
            legend: {
                display: true
            },
            title: {
                display: true,
                text: 'Supporters Per Ward'
            }
        },

        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    precision: 0
                },
                title: {
                    display: true,
                    text: 'Number of Supporters'
                }
            },
            x: {
                title: {
                    display: true,
                    text: 'Ward'
                }
            }
        }
    }
});

</script>
</body>
</html>