<?php
session_start();

$title       = "ChairHive - About";
$currentPage = "about";

$groupMembers = array(
    array("name" => "Joschka Atabelo",   "role" => "Project Manager"),
    array("name" => "Art Panulde",     "role" => "Frontend Developer"),
    array("name" => "Jose Soriano",      "role" => "Backend Developer"),
    array("name" => "Carl Vitalista",        "role" => "UI / UX Designer"),
   
);

require('include/header.php');
?>

<!-- Page Head -->
<div class="cv-page-head">
    <div class="cv-page-head-inner">
        <h1>About ChairHive</h1>
        <p>Our company, our focus, and the team behind this project.</p>
    </div>
</div>

<div style="max-width:1200px;margin:0 auto;padding:56px 40px 80px;">

    <!-- Company Info -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="cv-card h-100">
                <p class="cv-section-label">Our Story</p>
                <h3 class="cv-card-title" style="font-size:1.4rem;">One focus: the chair you sit in.</h3>
                <p class="text-muted" style="line-height:1.75;font-size:0.92rem;">
                    ChairHive was built around a single idea &mdash; the chair you sit in matters more than almost any other
                    piece of office equipment. We specialize exclusively in seating, so every product we carry is chosen
                    for comfort, durability, and proper ergonomic support.
                </p>
                <p class="text-muted" style="line-height:1.75;font-size:0.92rem;">
                    Whether you need an all-day ergonomic chair, a high-back executive seat for the boardroom,
                    a gaming chair for late sessions, or simple guest seating &mdash; ChairHive has a chair built for the job.
                </p>
            </div>
        </div>
        <div class="col-md-6">
            <div class="cv-card h-100">
                <p class="cv-section-label">What We Offer</p>
                <h3 class="cv-card-title" style="font-size:1.4rem;">Five categories of seating.</h3>
                <ul style="color:#475569;font-size:0.92rem;line-height:2;padding-left:18px;">
                    <li><strong>Ergonomic Chairs</strong> &mdash; Mesh backs, lumbar support, all-day comfort</li>
                    <li><strong>Executive Chairs</strong> &mdash; Leather seating for offices and boardrooms</li>
                    <li><strong>Gaming Chairs</strong> &mdash; Racing-style with headrests and footrests</li>
                    <li><strong>Visitor & Guest Chairs</strong> &mdash; Stackable, waiting-room, and bench seating</li>
                    <li><strong>Stools & Drafting Chairs</strong> &mdash; Height-adjustable for standing desks and counters</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Team Section -->
    <div class="mb-2">
        <p class="cv-section-label">The Team</p>
        <h2 class="cv-section-title">Meet CyberVision</h2>
        <p class="cv-section-sub">The group behind this Web Development final project.</p>
    </div>

    <div class="row row-cols-1 row-cols-md-3 g-4">
        <?php foreach ($groupMembers as $member):
            $nameParts = explode(' ', $member['name']);
            $initials  = '';
            foreach ($nameParts as $part) {
                $initials .= strtoupper(substr($part, 0, 1));
            }
        ?>
            <div class="col">
                <div class="cv-card text-center h-100">
                    <div class="rounded-circle text-white fw-bold d-flex align-items-center justify-content-center mx-auto mb-3"
                         style="width:62px;height:62px;font-size:1.15rem;background-color:#0E7490;">
                        <?= htmlspecialchars($initials) ?>
                    </div>
                    <h6 class="fw-bold mb-1"><?= htmlspecialchars($member['name']) ?></h6>
                    <p class="text-muted small mb-0"><?= htmlspecialchars($member['role']) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

</div>

<?php require('include/footer.php'); ?>
