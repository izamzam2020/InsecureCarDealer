<?php
require __DIR__ . '/includes/config.php';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/nav.php';
?>

<main>
  <section class="container">
    <div class="section-header reveal">
      <h2>Customer Reviews</h2>
      <p>What our customers say about Speedy Motors.</p>
      <div style="margin-top:10px;">
        <button class="btn btn--primary" id="openReviewModal">Add Review</button>
      </div>
    </div>

    <div class="cards">
      <?php if (!$DB_CONNECTED) { ?>
        <article class="card reveal">
          <div class="card__body">
            <h3>You must connect to the database</h3>
            <p>Enter the details in /includes/config.php</p>
          </div>
        </article>
      <?php } else { ?>
        <?php
          $res = mysqli_query($conn, "SELECT customer_name, rating, title, body, image, created_at FROM reviews ORDER BY created_at DESC, id DESC");
          if ($res && mysqli_num_rows($res) > 0) {
            while ($row = mysqli_fetch_assoc($res)) {
              $name = $row['customer_name'];
              $rating = max(1, min(5, (int)$row['rating']));
              $title = $row['title'];
              $body = $row['body'];
              $date = date('M j, Y', strtotime($row['created_at']));
        ?>
          <article class="card reveal">
            <div class="card__body">
              <div class="card__meta" style="margin-bottom:8px;">
                <strong><?php echo $name; ?></strong>
                <span class="muted" style="margin-left:8px;">&nbsp;<?php echo $date; ?></span>
              </div>
              <h3 style="margin:0 0 6px;">&nbsp;<?php echo $title; ?></h3>
              <p style="margin:0 0 10px;">&nbsp;<?php echo $body; ?></p>
              <?php if (!empty($row['image'])) { ?>
                <div class="card__media" style="margin:10px 0;">
                  <img src="<?php echo $row['image']; ?>" alt="review image" style="max-width:100%; height:auto; border-radius:8px;">
                </div>
              <?php } ?>
              <div class="rating" aria-label="Rating: <?php echo $rating; ?> out of 5">
                <?php for ($i=1; $i<=5; $i++) { ?>
                  <span class="star" style="color:<?php echo $i <= $rating ? '#f59e0b' : '#374151'; ?>;">★</span>
                <?php } ?>
              </div>
            </div>
          </article>
        <?php
            }
          } else {
        ?>
          <article class="card reveal">
            <div class="card__body">
              <h3>No reviews yet</h3>
              <p>Run the <a href="<?php echo htmlspecialchars(base_url('seed.php')); ?>">seed script</a> to add demo reviews.</p>
            </div>
          </article>
        <?php } ?>
      <?php } ?>
    </div>
  </section>

  <!-- Add Review Modal -->
  <div id="reviewModal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.6); z-index:1000;">
    <div role="dialog" aria-modal="true" aria-labelledby="reviewModalTitle" style="background:#111827; border:1px solid #1f2430; border-radius:12px; width:min(600px, 92%); margin:8vh auto; padding:16px; color:#e5e7eb;">
      <div style="display:flex; align-items:center; justify-content:space-between;">
        <h3 id="reviewModalTitle" style="margin:0;">Add Review</h3>
        <button id="closeReviewModal" class="btn btn--ghost" aria-label="Close" style="margin-left:12px;">×</button>
      </div>
      <form id="reviewForm" style="margin-top:12px;" enctype="multipart/form-data">
        <div style="margin-bottom:10px;">
          <label for="rating" style="display:block; margin-bottom:6px;">Stars (1-5)</label>
          <div id="starPicker" style="font-size:22px; cursor:pointer; user-select:none;">
            <span data-v="1">☆</span>
            <span data-v="2">☆</span>
            <span data-v="3">☆</span>
            <span data-v="4">☆</span>
            <span data-v="5">☆</span>
          </div>
          <input type="hidden" id="rating" name="rating" value="5">
        </div>
        <div style="margin-bottom:10px;">
          <label for="title" style="display:block; margin-bottom:6px;">Title</label>
          <input id="title" name="title" type="text" style="width:100%; padding:8px; background:#0b0d10; border:1px solid #1f2430; color:#e5e7eb; border-radius:8px;" placeholder="e.g., Excellent service">
        </div>
        <div style="margin-bottom:10px;">
          <label for="body" style="display:block; margin-bottom:6px;">Description</label>
          <textarea id="body" name="body" rows="5" style="width:100%; padding:8px; background:#0b0d10; border:1px solid #1f2430; color:#e5e7eb; border-radius:8px;" placeholder="Share your experience..."></textarea>
        </div>
        <div style="margin-bottom:10px;">
          <label for="image" style="display:block; margin-bottom:6px;">Image</label>
          <input id="image" name="image" type="file" accept="image/*" style="width:100%;">
        </div>
        <div style="display:flex; gap:10px;">
          <button type="submit" class="btn btn--primary">Save Review</button>
          <button type="button" id="cancelReview" class="btn btn--ghost">Cancel</button>
        </div>
      </form>
    </div>
  </div>

  <script>
  (function(){
    var modal = document.getElementById('reviewModal');
    var openBtn = document.getElementById('openReviewModal');
    var closeBtn = document.getElementById('closeReviewModal');
    var cancelBtn = document.getElementById('cancelReview');
    var form = document.getElementById('reviewForm');
    var starPicker = document.getElementById('starPicker');
    var ratingInput = document.getElementById('rating');

    function openModal(){ modal.style.display = 'block'; }
    function closeModal(){ modal.style.display = 'none'; }
    if (openBtn) openBtn.addEventListener('click', openModal);
    if (closeBtn) closeBtn.addEventListener('click', closeModal);
    if (cancelBtn) cancelBtn.addEventListener('click', closeModal);
    if (modal) modal.addEventListener('click', function(e){ if (e.target === modal) closeModal(); });

    if (starPicker) {
      function renderStars(val){
        var stars = starPicker.querySelectorAll('span');
        stars.forEach(function(s){
          var v = parseInt(s.getAttribute('data-v'));
          s.textContent = v <= val ? '★' : '☆';
          s.style.color = v <= val ? '#f59e0b' : '#374151';
        });
      }
      renderStars(parseInt(ratingInput.value || '5'));
      starPicker.addEventListener('click', function(e){
        var t = e.target;
        if (t && t.hasAttribute('data-v')) {
          var val = parseInt(t.getAttribute('data-v'));
          ratingInput.value = String(val);
          renderStars(val);
        }
      });
    }

    if (form) {
      form.addEventListener('submit', function(e){
        e.preventDefault();
        var fd = new FormData(form);
        fetch('<?php echo htmlspecialchars(base_url('add_review.php')); ?>', {
          method: 'POST',
          body: fd
        }).then(function(r){ return r.json(); })
          .then(function(j){
            if (j && j.success) {
              window.location.reload();
            } else {
              alert('Failed to save review');
            }
          }).catch(function(){ alert('Error saving review'); });
      });
    }
  })();
  </script>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>



