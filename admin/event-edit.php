<?php
/**
 * Xlator Admin - Create / Edit Event
 * Supports file uploads from gallery/device + preset library picker
 */
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/events-db.php';

require_admin_login();

$id = $_GET['id'] ?? '';
$is_edit = !empty($id);
$event = $is_edit ? get_event_by_id($id) : null;

if ($is_edit && !$event) {
    set_flash_message('error', 'Event not found.');
    header('Location: /admin/index.php');
    exit;
}

$error = '';

// Process Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        $error = 'Invalid security token. Please try again.';
    } else {
        $title = trim($_POST['title'] ?? '');
        $date = trim($_POST['date'] ?? '');

        if (empty($title)) {
            $error = 'Event Title is required.';
        } elseif (empty($date)) {
            $error = 'Event Date is required.';
        } else {
            // Determine cover image (defaulting to existing selection)
            $final_image = trim($_POST['image'] ?? 'images/collaboration.jpg');
            if (empty($final_image)) {
                $final_image = 'images/collaboration.jpg';
            }

            // Handle file upload from Device / Photo Gallery
            if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
                $file = $_FILES['image_file'];
                if ($file['error'] === UPLOAD_ERR_OK) {
                    $allowed_exts = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
                    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                    $max_size = 10 * 1024 * 1024; // 10MB limit

                    if (!in_array($file_ext, $allowed_exts)) {
                        $error = 'Invalid image format. Please upload JPG, PNG, WEBP, or GIF.';
                    } elseif ($file['size'] > $max_size) {
                        $error = 'Image file too large. Maximum allowed size is 10MB.';
                    } else {
                        // Validate that file is actually an image
                        $img_info = @getimagesize($file['tmp_name']);
                        if ($img_info === false) {
                            $error = 'The uploaded file is not a valid image.';
                        } else {
                            $uploads_dir = __DIR__ . '/../uploads/events';
                            if (!is_dir($uploads_dir)) {
                                @mkdir($uploads_dir, 0755, true);
                            }

                            // Generate clean, unique filename
                            $raw_title = !empty($_POST['slug']) ? $_POST['slug'] : $title;
                            $clean_base = preg_replace('/[^a-zA-Z0-9_-]/', '-', strtolower($raw_title));
                            $clean_base = substr($clean_base, 0, 30);
                            $new_filename = 'event_' . time() . '_' . $clean_base . '.' . $file_ext;
                            $target_dest = $uploads_dir . '/' . $new_filename;

                            if (move_uploaded_file($file['tmp_name'], $target_dest)) {
                                $final_image = 'uploads/events/' . $new_filename;
                            } else {
                                $error = 'Failed to save uploaded image. Check directory write permissions on host.';
                            }
                        }
                    }
                } else {
                    $error = 'File upload failed with error code: ' . $file['error'];
                }
            }

            if (empty($error)) {
                $data = [
                    'id'           => $is_edit ? $id : '',
                    'title'        => $title,
                    'slug'         => trim($_POST['slug'] ?? ''),
                    'category'     => trim($_POST['category'] ?? 'Offline Session'),
                    'summary'      => trim($_POST['summary'] ?? ''),
                    'description'  => trim($_POST['description'] ?? ''),
                    'date'         => $date,
                    'time'         => trim($_POST['time'] ?? '10:00 am – 5:00 pm'),
                    'location'     => trim($_POST['location'] ?? 'Kochi, Kerala'),
                    'format'       => trim($_POST['format'] ?? 'Offline'),
                    'image'        => $final_image,
                    'is_published' => isset($_POST['is_published'])
                ];

                save_event($data);
                set_flash_message('success', $is_edit ? 'Event updated successfully!' : 'New event created successfully!');
                header('Location: /admin/index.php');
                exit;
            }
        }
    }
}

// Preset values for form
$form_title = $_POST['title'] ?? ($event['title'] ?? '');
$form_slug = $_POST['slug'] ?? ($event['slug'] ?? '');
$form_category = $_POST['category'] ?? ($event['category'] ?? 'Offline Session');
$form_date = $_POST['date'] ?? ($event['date'] ?? date('Y-m-d'));
$form_time = $_POST['time'] ?? ($event['time'] ?? '10:00 am – 5:00 pm');
$form_format = $_POST['format'] ?? ($event['format'] ?? 'Offline');
$form_location = $_POST['location'] ?? ($event['location'] ?? 'Kochi, Kerala');
$form_summary = $_POST['summary'] ?? ($event['summary'] ?? '');
$form_description = $_POST['description'] ?? ($event['description'] ?? '');
$form_image = $_POST['image'] ?? ($event['image'] ?? 'images/collaboration.jpg');
$form_published = isset($_POST['is_published']) ? true : ($is_edit ? !empty($event['is_published']) : true);

// Curated stock library presets
$preset_images = [
    'images/collaboration.jpg'         => 'Collaboration Table',
    'images/team-together.jpg'         => 'Team Together Laptops',
    'images/business-presentation.jpg' => 'Business Presentation',
    'images/staircase-growth.jpg'      => 'Staircase Growth',
    'images/building-blocks.jpg'       => 'Building Blocks',
    'images/community.jpg'             => 'Community Gathering',
    'images/hero-handshake.jpg'        => 'Handshake Growth',
    'images/dashboard-work.jpg'        => 'Dashboard Workspace'
];

// Build full gallery: Presets + Uploads
$gallery_items = [];
foreach ($preset_images as $path => $label) {
    $gallery_items[] = [
        'path'      => $path,
        'label'     => $label,
        'type'      => 'Preset',
        'is_upload' => false
    ];
}

$uploads_dir = __DIR__ . '/../uploads/events';
if (is_dir($uploads_dir)) {
    $uploaded_files = scandir($uploads_dir);
    foreach ($uploaded_files as $f) {
        if ($f === '.' || $f === '..') continue;
        $ext = strtolower(pathinfo($f, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
            $gallery_items[] = [
                'path'      => 'uploads/events/' . $f,
                'label'     => $f,
                'type'      => 'Uploaded',
                'is_upload' => true
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <title><?php echo $is_edit ? 'Edit Event' : 'Create New Event'; ?> — Xlator Admin</title>
  <base href="/"/>
  <link rel="icon" type="image/png" href="/images/favicon.png"/>
  <link rel="preconnect" href="https://fonts.googleapis.com"/>
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin="anonymous"/>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Archivo:wght@400;500;600;700;800;900&family=Barlow+Condensed:wght@400;500;600&display=swap"/>
  <link rel="stylesheet" href="/css/styles.css"/>
  <link rel="stylesheet" href="/css/admin.css"/>
</head>
<body class="bg-background text-foreground min-h-screen flex flex-col selection:bg-brand selection:text-brand-foreground">

  <!-- Admin Header -->
  <header class="border-b border-border/70 bg-card py-4 px-6 md:px-10">
    <div class="mx-auto max-w-5xl flex items-center justify-between">
      <div class="flex items-center gap-4">
        <a href="/admin/index.php" class="eyebrow text-xs hover:text-brand transition-colors inline-flex items-center gap-1.5">
          &larr; Back to Events
        </a>
      </div>
      <span class="text-xs text-muted-foreground">
        <?php echo $is_edit ? 'Editing: <strong>' . htmlspecialchars($form_title, ENT_QUOTES, 'UTF-8') . '</strong>' : 'New Event Draft'; ?>
      </span>
    </div>
  </header>

  <main class="flex-1 py-10 px-5 md:px-10">
    <div class="mx-auto max-w-5xl">

      <div class="mb-8">
        <p class="eyebrow text-brand"><?php echo $is_edit ? 'Update Event Record' : 'Add New Event'; ?></p>
        <h1 class="headline mt-2 text-3xl md:text-5xl"><?php echo $is_edit ? 'Edit Event' : 'Create New Event'; ?></h1>
        <p class="mt-2 text-muted-foreground">Fill in the event information and upload custom photography from your gallery or choose presets.</p>
      </div>

      <?php if (!empty($error)): ?>
        <div class="mb-6 rounded-2xl border border-brand/40 bg-brand/10 p-4 text-sm text-brand font-medium">
          <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form method="POST" action="/admin/event-edit.php<?php echo $is_edit ? '?id=' . urlencode($id) : ''; ?>" enctype="multipart/form-data" class="space-y-8" id="eventForm">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(get_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>" />

        <!-- Card 1: Core Details -->
        <div class="rounded-3xl border border-border bg-card p-6 md:p-8 shadow-[var(--shadow-lift)] space-y-6">
          <h2 class="headline text-xl pb-3 border-b border-border">Core Information</h2>

          <!-- Event Title -->
          <div>
            <label class="eyebrow text-[0.65rem] font-medium" for="title">Event Title *</label>
            <input type="text" id="title" name="title" required value="<?php echo htmlspecialchars($form_title, ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g. Brand Positioning Masterclass" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2 h-11"/>
          </div>

          <!-- Slug & Category Grid -->
          <div class="grid gap-6 sm:grid-cols-2">
            <div>
              <label class="eyebrow text-[0.65rem] font-medium" for="slug">URL Slug (leave blank to auto-generate)</label>
              <input type="text" id="slug" name="slug" value="<?php echo htmlspecialchars($form_slug, ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g. brand-positioning-masterclass" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2 h-11"/>
            </div>

            <div>
              <label class="eyebrow text-[0.65rem] font-medium" for="category">Category Badge *</label>
              <select id="category" name="category" class="flex w-full rounded-md border border-input bg-card px-3 py-1 text-base shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2 h-11">
                <option value="Offline Session" <?php echo $form_category === 'Offline Session' ? 'selected' : ''; ?>>Offline Session</option>
                <option value="Program Info Session" <?php echo $form_category === 'Program Info Session' ? 'selected' : ''; ?>>Program Info Session</option>
                <option value="Elite Session" <?php echo $form_category === 'Elite Session' ? 'selected' : ''; ?>>Elite Session</option>
                <option value="Workshop" <?php echo $form_category === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                <option value="Masterclass" <?php echo $form_category === 'Masterclass' ? 'selected' : ''; ?>>Masterclass</option>
                <option value="Mentorship Clinic" <?php echo $form_category === 'Mentorship Clinic' ? 'selected' : ''; ?>>Mentorship Clinic</option>
              </select>
            </div>
          </div>

          <!-- Date, Time, Format & Location Grid -->
          <div class="grid gap-6 sm:grid-cols-2 md:grid-cols-4">
            <div>
              <label class="eyebrow text-[0.65rem] font-medium" for="date">Event Date *</label>
              <input type="date" id="date" name="date" required value="<?php echo htmlspecialchars($form_date, ENT_QUOTES, 'UTF-8'); ?>" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2 h-11"/>
            </div>

            <div>
              <label class="eyebrow text-[0.65rem] font-medium" for="time">Time (e.g. 10:00 am – 5:00 pm)</label>
              <input type="text" id="time" name="time" value="<?php echo htmlspecialchars($form_time, ENT_QUOTES, 'UTF-8'); ?>" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2 h-11"/>
            </div>

            <div>
              <label class="eyebrow text-[0.65rem] font-medium" for="format">Format *</label>
              <select id="format" name="format" class="flex w-full rounded-md border border-input bg-card px-3 py-1 text-base shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2 h-11">
                <option value="Offline" <?php echo $form_format === 'Offline' ? 'selected' : ''; ?>>Offline</option>
                <option value="Online" <?php echo $form_format === 'Online' ? 'selected' : ''; ?>>Online</option>
              </select>
            </div>

            <div>
              <label class="eyebrow text-[0.65rem] font-medium" for="location">Location</label>
              <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($form_location, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Kochi, Kerala" class="flex w-full rounded-md border border-input bg-transparent px-3 py-1 text-base shadow-sm transition-colors focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2 h-11"/>
            </div>
          </div>
        </div>

        <!-- Card 2: Descriptions -->
        <div class="rounded-3xl border border-border bg-card p-6 md:p-8 shadow-[var(--shadow-lift)] space-y-6">
          <h2 class="headline text-xl pb-3 border-b border-border">Content &amp; Description</h2>

          <!-- Short Summary -->
          <div>
            <label class="eyebrow text-[0.65rem] font-medium" for="summary">Short Summary (Shown on event card preview) *</label>
            <textarea id="summary" name="summary" rows="2" maxlength="300" placeholder="A concise 1-2 sentence overview of this event..." class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2"><?php echo htmlspecialchars($form_summary, ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>

          <!-- Full Description -->
          <div>
            <label class="eyebrow text-[0.65rem] font-medium" for="description">Full Description (Shown on event detail page)</label>
            <textarea id="description" name="description" rows="6" placeholder="Provide complete session details, key takeaways, and who should attend..." class="flex w-full rounded-md border border-input bg-transparent px-3 py-2 text-base shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring md:text-sm mt-2"><?php echo htmlspecialchars($form_description, ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>
        </div>

        <!-- Card 3: Media & Status (Upload from Gallery / Device or Choose Preset) -->
        <div class="rounded-3xl border border-border bg-card p-6 md:p-8 shadow-[var(--shadow-lift)] space-y-6">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-border gap-2">
            <div>
              <h2 class="headline text-xl">Media &amp; Cover Image</h2>
              <p class="text-xs text-muted-foreground mt-0.5">Upload custom photography from your device/gallery or pick from our library.</p>
            </div>
            <span class="eyebrow text-[0.65rem] text-brand">Recommended: 16:10 or 16:9 ratio</span>
          </div>

          <!-- Master Cover Image Preview & Selector Layout (2-Column Grid) -->
          <div class="media-editor-grid">
            
            <!-- Left Column: Active Image Live Preview -->
            <div class="preview-card-wrap">
              <div class="flex items-center justify-between">
                <label class="eyebrow text-[0.65rem] font-semibold text-foreground">Active Cover Preview</label>
                <span id="sourceBadge" class="px-2 py-0.5 rounded-full text-[0.6rem] font-bold uppercase tracking-wider bg-brand/10 text-brand">
                  Live Selection
                </span>
              </div>

              <div class="master-preview-box">
                <img id="masterImagePreview" src="<?php echo htmlspecialchars(get_asset_url($form_image, true), ENT_QUOTES, 'UTF-8'); ?>" alt="Cover Preview" />
                <div class="master-preview-overlay">
                  <div class="min-w-0 pr-2">
                    <p class="text-[0.62rem] uppercase tracking-wider text-white/70 font-semibold mb-0.5">Current Path</p>
                    <p id="masterImageLabel" class="preview-path-text"><?php echo htmlspecialchars($form_image, ENT_QUOTES, 'UTF-8'); ?></p>
                  </div>
                  <span id="previewTag" class="text-[0.6rem] bg-white/20 backdrop-blur px-2 py-0.5 rounded text-white shrink-0 font-medium">Current</span>
                </div>
              </div>

              <!-- Grayscale filter preview toggle -->
              <div class="grayscale-toggle-bar">
                <label class="flex items-center gap-2 cursor-pointer select-none">
                  <input type="checkbox" id="previewMonoToggle" onchange="toggleMonoPreview(this.checked)" class="size-4 rounded border-border text-brand focus:ring-brand"/>
                  <span class="font-medium text-foreground">Test Xlator Grayscale Style</span>
                </label>
                <span class="text-[0.7rem] text-muted-foreground">Matches live site look</span>
              </div>
            </div>

            <!-- Right Column: Tabs for File Upload & Existing Gallery -->
            <div class="space-y-4">
              
              <!-- Tab Controls -->
              <div class="media-tab-container">
                <button type="button" id="tabUploadBtn" onclick="switchMediaTab('upload')" class="media-tab-btn active">
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                    <polyline points="17 8 12 3 7 8"></polyline>
                    <line x1="12" y1="3" x2="12" y2="15"></line>
                  </svg>
                  Upload from Gallery / Files
                </button>
                <button type="button" id="tabGalleryBtn" onclick="switchMediaTab('gallery')" class="media-tab-btn">
                  <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                    <circle cx="8.5" cy="8.5" r="1.5"></circle>
                    <polyline points="21 15 16 10 5 21"></polyline>
                  </svg>
                  Choose from Gallery Library
                </button>
              </div>

              <!-- PANE 1: Upload from Gallery / Device -->
              <div id="paneUpload" class="space-y-3">
                <div id="dropZone" class="upload-dropzone" onclick="document.getElementById('image_file').click()">
                  <input type="file" id="image_file" name="image_file" accept="image/png,image/jpeg,image/jpg,image/webp,image/gif" class="hidden" onchange="handleFileSelect(this)"/>
                  
                  <div class="upload-icon-circle">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                      <polyline points="17 8 12 3 7 8"></polyline>
                      <line x1="12" y1="3" x2="12" y2="15"></line>
                    </svg>
                  </div>
                  
                  <h3 class="upload-headline">Click to upload from gallery or drag &amp; drop</h3>
                  <p class="upload-subtext">Accepts JPG, PNG, WEBP, or GIF &middot; Max 10MB</p>
                  
                  <div>
                    <span class="upload-cta-btn">
                      <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                      </svg>
                      Choose Image from Gallery
                    </span>
                  </div>
                </div>

                <!-- Pending File Upload Confirmation Bar -->
                <div id="pendingUploadInfo" class="hidden pending-upload-bar">
                  <div class="flex items-center gap-3 min-w-0">
                    <div class="pending-badge-check">
                      ✓
                    </div>
                    <div class="min-w-0 text-xs">
                      <p class="font-semibold text-emerald-900 truncate" id="uploadFileName">image.jpg</p>
                      <p class="text-emerald-700 text-[0.7rem]" id="uploadFileSize">Ready to upload on save</p>
                    </div>
                  </div>
                  <button type="button" onclick="cancelFileUpload()" class="px-3 py-1.5 rounded-lg border border-border bg-card hover:bg-destructive hover:text-white text-xs font-semibold transition-colors cursor-pointer">
                    Clear / Change
                  </button>
                </div>
              </div>

              <!-- PANE 2: Visual Library Gallery Grid -->
              <div id="paneGallery" class="hidden space-y-3">
                <div class="flex items-center justify-between text-xs px-1">
                  <span class="text-muted-foreground">Click any thumbnail below to select:</span>
                  <span class="eyebrow text-[0.65rem] text-muted-foreground"><?php echo count($gallery_items); ?> Images in Library</span>
                </div>

                <div class="gallery-picker-grid">
                  <?php foreach ($gallery_items as $item): 
                    $is_active = ($form_image === $item['path']);
                  ?>
                    <div class="gallery-card <?php echo $is_active ? 'active' : ''; ?>"
                         onclick="selectGalleryImage('<?php echo htmlspecialchars($item['path'], ENT_QUOTES, 'UTF-8'); ?>', '<?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>', this)">
                      <div class="gallery-card-thumb">
                        <img src="<?php echo htmlspecialchars(get_asset_url($item['path'], true), ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?>" loading="lazy" />
                      </div>
                      <div class="gallery-card-meta">
                        <p class="gallery-card-name"><?php echo htmlspecialchars($item['label'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <span class="gallery-card-tag"><?php echo $item['type']; ?></span>
                      </div>
                      
                      <!-- Selected Check Indicator -->
                      <div class="gallery-check-icon">
                        ✓
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              </div>

              <!-- Hidden input that preserves selected gallery path -->
              <input type="hidden" id="selected_image" name="image" value="<?php echo htmlspecialchars($form_image, ENT_QUOTES, 'UTF-8'); ?>" />

            </div>
          </div>

          <!-- Published Toggle -->
          <div class="flex items-center gap-3 pt-4 border-t border-border">
            <input type="checkbox" id="is_published" name="is_published" value="1" <?php echo $form_published ? 'checked' : ''; ?> class="size-5 rounded border-border text-brand focus:ring-brand"/>
            <label for="is_published" class="text-sm font-semibold cursor-pointer">
              Published (Visible to public on the live website)
            </label>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center justify-end gap-4 pt-4">
          <a href="/admin/index.php" class="inline-flex items-center justify-center rounded-full border border-border px-6 py-3 text-sm font-semibold hover:bg-muted transition-colors">
            Cancel
          </a>
          <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-full bg-brand text-brand-foreground uppercase tracking-wider font-semibold hover:bg-brand/90 shadow-[var(--shadow-lift)] h-12 px-8 text-sm cursor-pointer">
            <?php echo $is_edit ? 'Save Changes' : 'Create Event'; ?>
          </button>
        </div>
      </form>

    </div>
  </main>

  <footer class="border-t border-border/70 py-6 px-6 text-center text-xs text-muted-foreground">
    &copy; <?php echo date('Y'); ?> Xlator Content Management System &middot; Brand Swamy&reg;
  </footer>

  <script>
    // Tab switching between Upload & Gallery
    function switchMediaTab(tab) {
      const paneUpload = document.getElementById('paneUpload');
      const paneGallery = document.getElementById('paneGallery');
      const tabUploadBtn = document.getElementById('tabUploadBtn');
      const tabGalleryBtn = document.getElementById('tabGalleryBtn');

      if (tab === 'upload') {
        paneUpload.classList.remove('hidden');
        paneGallery.classList.add('hidden');
        tabUploadBtn.classList.add('active');
        tabGalleryBtn.classList.remove('active');
      } else {
        paneUpload.classList.add('hidden');
        paneGallery.classList.remove('hidden');
        tabGalleryBtn.classList.add('active');
        tabUploadBtn.classList.remove('active');
      }
    }

    // Toggle photo-mono grayscale filter on live preview
    function toggleMonoPreview(enabled) {
      const img = document.getElementById('masterImagePreview');
      if (enabled) {
        img.classList.add('photo-mono');
      } else {
        img.classList.remove('photo-mono');
      }
    }

    // Selection from Visual Gallery
    function selectGalleryImage(path, label, cardEl) {
      // Update hidden input
      document.getElementById('selected_image').value = path;
      
      // Update master preview
      const previewImg = document.getElementById('masterImagePreview');
      previewImg.src = '/' + path.replace(/^\/+/, '');
      document.getElementById('masterImageLabel').textContent = path;
      document.getElementById('sourceBadge').textContent = 'Library Item';
      document.getElementById('sourceBadge').className = 'px-2 py-0.5 rounded-full text-[0.6rem] font-bold uppercase tracking-wider bg-ink text-ink-foreground';
      document.getElementById('previewTag').textContent = label;

      // Update active cards UI
      document.querySelectorAll('.gallery-card').forEach(c => {
        c.classList.remove('active');
      });

      if (cardEl) {
        cardEl.classList.add('active');
      }

      // If user had selected a file for upload, clear it
      cancelFileUpload(false);
    }

    // File input selection handler
    function handleFileSelect(input) {
      if (input.files && input.files[0]) {
        const file = input.files[0];

        // Format file size
        const sizeFormatted = file.size > 1048576 
          ? (file.size / 1048576).toFixed(2) + ' MB'
          : (file.size / 1024).toFixed(1) + ' KB';

        // Update preview with local file blob
        const objectUrl = URL.createObjectURL(file);
        const previewImg = document.getElementById('masterImagePreview');
        previewImg.src = objectUrl;

        document.getElementById('masterImageLabel').textContent = file.name;
        document.getElementById('sourceBadge').textContent = 'Upload Pending';
        document.getElementById('sourceBadge').className = 'px-2 py-0.5 rounded-full text-[0.6rem] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-700';
        document.getElementById('previewTag').textContent = sizeFormatted;

        // Show pending upload confirmation
        document.getElementById('pendingUploadInfo').classList.remove('hidden');
        document.getElementById('uploadFileName').textContent = file.name;
        document.getElementById('uploadFileSize').textContent = sizeFormatted + ' • Will save on submit';

        // Deselect gallery cards
        document.querySelectorAll('.gallery-card').forEach(c => {
          c.classList.remove('active');
        });
      }
    }

    // Cancel pending file upload and revert
    function cancelFileUpload(restoreGallery = true) {
      const fileInput = document.getElementById('image_file');
      fileInput.value = '';
      document.getElementById('pendingUploadInfo').classList.add('hidden');

      if (restoreGallery) {
        const currentPath = document.getElementById('selected_image').value || 'images/collaboration.jpg';
        const previewImg = document.getElementById('masterImagePreview');
        previewImg.src = '/' + currentPath.replace(/^\/+/, '');
        document.getElementById('masterImageLabel').textContent = currentPath;
        document.getElementById('sourceBadge').textContent = 'Live Selection';
        document.getElementById('sourceBadge').className = 'px-2 py-0.5 rounded-full text-[0.6rem] font-bold uppercase tracking-wider bg-brand/10 text-brand';
        document.getElementById('previewTag').textContent = 'Current';

        // Re-highlight matching gallery card if present
        document.querySelectorAll('.gallery-card').forEach(c => {
          if (c.getAttribute('onclick') && c.getAttribute('onclick').includes(currentPath)) {
            c.classList.add('active');
          }
        });
      }
    }

    // Setup drag & drop on dropZone
    const dropZone = document.getElementById('dropZone');
    if (dropZone) {
      ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropZone.classList.add('dragover');
        }, false);
      });

      ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, (e) => {
          e.preventDefault();
          e.stopPropagation();
          dropZone.classList.remove('dragover');
        }, false);
      });

      dropZone.addEventListener('drop', (e) => {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files && files.length > 0) {
          const fileInput = document.getElementById('image_file');
          fileInput.files = files;
          handleFileSelect(fileInput);
        }
      }, false);
    }
  </script>
</body>
</html>