<?php
/*
  EOUDIA ARCHIVE - Stainless Steel Jewelry Website

  Folder structure (inside the XAMPP htdocs folder):

  htdocs/
  └──/25081494254_TALITHASASTAVALENTINA_PWeb
      ├── index_eoudiaarchives.php   <- this file (PHP + HTML + CSS)
      ├── counter.txt                <- created automatically (page view counter)
      └── images/
          ├── ea-001.jpg
          ├── ea-004.jpg
          ├── ea-005.jpg
          ├── ea-006.jpg
          ├── ea-007.jpg
          └── ea-009.jpg

  Run it: start Apache in XAMPP, then open
  http://localhost/25081494254_TALITHASASTAVALENTINA_PWeb/index_eoudiaarchives.php
*/

date_default_timezone_set("Asia/Jakarta");

// ===== Page View Counter =====
$file = "counter.txt";
if (!file_exists($file)) { file_put_contents($file, "0"); }
$views = (int) file_get_contents($file) + 1;
file_put_contents($file, $views);

// ===== Data =====
$products = [
  ["code" => "EA-001", "name" => "Monolith Signet Ring", "category" => "Ring", "price" => 189000, "stock" => 14, "desc" => "Flat-top signet in brushed 316L steel.", "img" => "images/ea-001.jpg"],

  ["code" => "EA-004", "name" => "Apex Cuff Bangle", "category" => "Bracelet", "price" => 235000, "stock" => 8, "desc" => "Open cuff with sharp, angular tips.", "img" => "images/ea-004.jpg"],
  ["code" => "EA-005", "name" => "Shard Cuff Ring", "category" => "Ring", "price" => 139000, "stock" => 6, "desc" => "Sleek cuff with a brushed and polished finish.", "img" => "images/ea-005.jpg"],
  ["code" => "EA-006", "name" => "Structure Curb Chain", "category" => "Bracelets", "price" => 215000, "stock" => 11, "desc" => "Polished flat-profile curb chain.", "img" => "images/ea-006.jpg"],
  ["code" => "EA-007", "name" => "Daily Curb Chain", "category" => "Necklace", "price" => 199000, "stock" => 15, "desc" => "Slim satin curb chain for everyday wear.", "img" => "images/ea-007.jpg"],
  
  ["code" => "EA-009", "name" => "Eclipse Orbit Pendant", "category" => "Necklace", "price" => 199000, "stock" => 9, "desc" => "Two polished rings hung on a fine cable chain.", "img" => "images/ea-009.jpg"]
];
$finishes = ["Polished Silver" => 0, "Matte Black PVD" => 20000, "Gunmetal Grey PVD" => 20000, "18K Gold PVD" => 30000];

function rupiah($value) {
  return "Rp" . number_format($value, 0, ",", ".");
}
function safe($text) {
  return htmlspecialchars($text, ENT_QUOTES, "UTF-8");
}

// Only products in stock can be ordered
$available = [];
foreach ($products as $p) {
  if ($p["stock"] > 0) { $available[$p["name"]] = $p["price"]; }
}

// ===== Search (GET) =====
$keyword = trim($_GET["keyword"] ?? "");
$found = 0;

// ===== Order (POST) =====
$selected = $_POST["product"] ?? $_GET["product"] ?? "";
$errors = [];
$success = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $name = trim($_POST["name"] ?? "");
  $email = trim($_POST["email"] ?? "");
  $product = $_POST["product"] ?? "";
  $finish = $_POST["finish"] ?? "";
  $quantity = filter_input(INPUT_POST, "quantity", FILTER_VALIDATE_INT);

  if ($name === "") { $errors[] = "Name is required."; }
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Email is not valid."; }
  if (!isset($available[$product])) { $errors[] = "Please choose a product that is in stock."; }
  if (!isset($finishes[$finish])) { $errors[] = "Please choose a finish."; }
  if ($quantity === false || $quantity === null || $quantity < 1) { $errors[] = "Quantity must be at least one."; }

  if (count($errors) === 0) {
    $unitPrice = $available[$product] + $finishes[$finish];
    $subtotal = $unitPrice * $quantity;
    $discount = $subtotal >= 400000 ? $subtotal * 0.10 : 0;
    $finalTotal = $subtotal - $discount;
    $orderId = "EA-" . date("ymd") . "-" . rand(100, 999);
    $orderDate = date("d F Y");
    $orderTime = date("H:i:s");
    $success = true;
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Eoudia Archive | Stainless Steel Jewelry</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Unbounded:wght@500;700&family=Manrope:wght@400;500;700&display=swap">
  <style>
:root { --bg:#e3e6e8; --ink:#0e1113; --panel:#14171a; --muted:#5d676e; --cobalt:#2b45ff; --line:#0e111333; }
* { box-sizing:border-box; }
html { scroll-behavior:smooth; scroll-padding-top:84px; }
body {
  margin:0; color:var(--ink); font-family:"Manrope",Arial,sans-serif; font-size:17px; line-height:1.65;
  background:var(--bg) repeating-linear-gradient(90deg, rgba(255,255,255,.22) 0 1px, transparent 1px 4px);
}
h1,h2,h3,.logo,.wordmark { font-family:"Unbounded","Arial Black",sans-serif; letter-spacing:-.02em; }
h2 { font-size:clamp(1.8rem,4vw,2.8rem); margin:0 0 20px; line-height:1.1; }
h3 { margin:0 0 6px; font-size:1.05rem; }
a { color:inherit; }
a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible { outline:3px solid var(--cobalt); outline-offset:3px; }
section { padding:72px 0; }
main { max-width:1120px; margin:0 auto; padding:0 24px; }

/* Header */
.topbar { position:sticky; top:0; z-index:10; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:8px 24px; padding:14px 28px; background:rgba(227,230,232,.82); backdrop-filter:blur(12px); border-bottom:1px solid var(--line); }
.logo { text-decoration:none; font-weight:700; font-size:1.05rem; }
nav a { text-decoration:none; font-weight:500; margin-left:26px; padding:4px 0; border-bottom:2px solid transparent; transition:border-color .2s; }
nav a:hover { border-bottom-color:var(--cobalt); }

/* Hero */
.hero { display:grid; grid-template-columns:1.1fr .9fr; align-items:center; gap:40px; padding:64px 0 48px; }
h1 { font-size:clamp(2.6rem,7vw,5.4rem); line-height:1; font-weight:700; margin:0 0 24px; }
.hero p { max-width:480px; font-size:1.15rem; }
.btn, button { display:inline-block; background:var(--cobalt); color:#fff; font:700 .95rem "Manrope",sans-serif; padding:14px 26px; border:0; border-radius:999px; text-decoration:none; cursor:pointer; transition:transform .2s, box-shadow .2s; }
.btn:hover, button:hover { transform:translateY(-3px); box-shadow:0 10px 22px rgba(43,69,255,.35); }
.btn.ghost { background:transparent; color:var(--ink); border:2px solid var(--ink); margin-left:8px; }
.btn.ghost:hover { box-shadow:none; background:var(--ink); color:#fff; }
.btn.small { padding:10px 20px; margin-top:8px; }

/* Photos */
.stage { position:relative; padding-bottom:40px; }
.shot { display:block; object-fit:cover; background:var(--panel); }
.shot.main { width:100%; aspect-ratio:4/5; border-radius:22px; box-shadow:0 28px 50px rgba(14,17,19,.4); }
.shot.inset { position:absolute; left:-16px; bottom:0; width:42%; aspect-ratio:1; border-radius:50%; border:6px solid var(--bg); box-shadow:0 18px 30px rgba(14,17,19,.4); }

/* Facts */
.facts { display:grid; grid-template-columns:repeat(auto-fit,minmax(220px,1fr)); border-top:2px solid var(--ink); border-bottom:1px solid var(--line); padding:0; }
.facts div { padding:24px 24px 24px 0; }
.facts div + div { padding-left:24px; border-left:1px solid var(--line); }
.facts p { margin:0; color:var(--muted); }

/* Products */
.search { display:flex; flex-wrap:wrap; align-items:center; gap:10px 14px; margin-bottom:12px; }
.search label { font-weight:700; }
.search input { width:240px; }
.search button { padding:11px 22px; }
input, select { width:100%; padding:12px 14px; font:inherit; border:1.5px solid var(--ink); border-radius:10px; background:#fff; }
.note { color:var(--muted); }
.grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(250px,1fr)); gap:26px; margin:26px 0 16px; }
.card { background:#fff; border-radius:18px; overflow:hidden; box-shadow:0 14px 30px rgba(14,17,19,.14); transition:transform .25s; }
.card:hover { transform:translateY(-6px); }
.piece { position:relative; overflow:hidden; aspect-ratio:4/5; background:var(--panel); }
.piece img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .6s; }
.card:hover .piece img { transform:scale(1.06); }
.code { position:absolute; left:14px; bottom:12px; padding:3px 10px; border-radius:999px; background:rgba(14,17,19,.7); backdrop-filter:blur(6px); color:#fff; font-weight:700; font-size:.8rem; letter-spacing:.06em; }
.info { padding:20px; }
.info p { margin:4px 0; font-size:.95rem; }
.cat { color:var(--cobalt); font-weight:700; }
.price { font-family:"Unbounded",sans-serif; font-size:1.1rem !important; font-weight:700; margin:10px 0 !important; }
.badge { display:inline-block; padding:3px 12px; border-radius:999px; font-weight:700; font-size:.82rem !important; }
.badge.ok { background:#d8f3e2; color:#14683a; } .badge.out { background:#f6d9d9; color:#9b2222; }
.card.sold .piece img { filter:grayscale(1); opacity:.45; }

/* Order */
.order { background:var(--panel); color:#fff; margin:0 -24px; padding:72px 24px; border-radius:28px; }
.order > * { max-width:560px; margin-left:auto; margin-right:auto; }
.order .note { color:#aab3b9; }
.order-form label { display:block; margin:16px 0 6px; font-weight:700; }
.order-form button { margin-top:24px; }
.alert { background:#3a1a1c; border-left:6px solid #ff5a5f; padding:12px 20px; margin:16px auto; border-radius:8px; }
.receipt { background:#fff; color:var(--ink); padding:28px; border-radius:16px; }
.receipt table { width:100%; border-collapse:collapse; margin:14px 0 18px; }
.receipt th, .receipt td { text-align:left; padding:9px 4px; border-bottom:1px solid var(--line); }
.receipt th { width:42%; color:var(--muted); font-weight:500; }
.receipt .total th, .receipt .total td { color:var(--cobalt); font-weight:700; font-size:1.1rem; border-bottom:0; }

/* About + footer */
.about-cols { display:grid; grid-template-columns:1.4fr 1fr; gap:40px; }
.about ul { padding-left:20px; }
footer { background:var(--ink); color:#aab3b9; margin-top:72px; padding:48px 28px 28px; }
.wordmark { color:#fff; font-size:clamp(2rem,9vw,6.5rem); font-weight:700; margin:0 0 20px; line-height:1; }
footer div { display:flex; justify-content:space-between; flex-wrap:wrap; gap:10px; border-top:1px solid #ffffff2e; padding-top:16px; }
footer p { margin:0; font-size:.9rem; }
.views { color:#fff; font-weight:700; }

@media (max-width:760px) {
  .hero, .about-cols { grid-template-columns:1fr; }
  nav a { margin:0 16px 0 0; }
  .facts div + div { padding-left:0; border-left:0; border-top:1px solid var(--line); }
}
@media (prefers-reduced-motion:reduce) { *, *::before { animation:none !important; transition:none !important; } html { scroll-behavior:auto; } }

</style>
</head>
<body>

<header class="topbar">
  <a class="logo" href="#home">Eoudia Archive</a>
  <nav>
    <a href="#home">Home</a>
    <a href="#products">Products</a>
    <a href="#order">Order</a>
    <a href="#about">About</a>
  </nav>
</header>

<main>
  <!-- HOME -->
  <section id="home" class="hero">
    <div class="hero-text">
      <h1>Buy once.<br>Wear forever.</h1>
      <p>Unisex jewelry in 316L stainless steel. It does not rust, does not turn black, and is safe for the shower and for sensitive skin.</p>
      <a class="btn" href="#products">Browse the archive</a>
      <a class="btn ghost" href="#order">Place an order</a>
    </div>
    <div class="stage">
      <img class="shot main" src="images/ea-002.jpg" alt="Slate Band Ring in brushed stainless steel">
      <img class="shot inset" src="images/ea-009.jpg" alt="Eclipse Orbit Pendant on a fine chain">
    </div>
  </section>

  <section class="facts">
    <div><h3>316L stainless steel</h3><p>Surgical-grade and hypoallergenic.</p></div>
    <div><h3>Shower-safe</h3><p>Water, sweat, and sea air will not dull it.</p></div>
    <div><h3>Tarnish-free guarantee</h3><p>If a piece rusts or tarnishes, we replace it.</p></div>
  </section>

  <!-- PRODUCTS -->
  <section id="products">
    <h2>The archive</h2>
    <form class="search" action="#products" method="get">
      <label for="keyword">Search product</label>
      <input type="text" id="keyword" name="keyword" value="<?php echo safe($keyword); ?>" placeholder="e.g. ring">
      <button type="submit">Search</button>
    </form>
    <?php if ($keyword !== "") { ?>
      <p class="note">Results for <strong><?php echo safe($keyword); ?></strong></p>
    <?php } ?>

    <div class="grid">
      <?php foreach ($products as $p) { ?>
        <?php if ($keyword === "" || stripos($p["name"], $keyword) !== false) { ?>
          <?php $found++; ?>
          <article class="card<?php echo $p["stock"] === 0 ? " sold" : ""; ?>">
            <div class="piece">
              <img src="<?php echo $p["img"]; ?>" alt="<?php echo safe($p["name"]); ?>" loading="lazy">
              <span class="code"><?php echo $p["code"]; ?></span>
            </div>
            <div class="info">
              <p class="cat"><?php echo $p["category"]; ?></p>
              <h3><?php echo $p["name"]; ?></h3>
              <p><?php echo $p["desc"]; ?></p>
              <p class="price"><?php echo rupiah($p["price"]); ?></p>
              <?php if ($p["stock"] > 0) { ?>
                <p class="badge ok">Available &middot; <?php echo $p["stock"]; ?> in stock</p>
                <a class="btn small" href="?product=<?php echo urlencode($p["name"]); ?>#order">Order this piece</a>
              <?php } else { ?>
                <p class="badge out">Out of Stock</p>
              <?php } ?>
            </div>
          </article>
        <?php } ?>
      <?php } ?>
    </div>
    <p class="note">Total products found: <?php echo $found; ?></p>
  </section>

  <!-- ORDER -->
  <section id="order" class="order">
    <h2>Place an order</h2>
    <p class="note">Orders with a subtotal of Rp400.000 or more get 10% off.</p>

    <?php if (count($errors) > 0) { ?>
      <div class="alert">
        <strong>Please fix the following:</strong>
        <ul>
          <?php foreach ($errors as $error) { ?>
            <li><?php echo safe($error); ?></li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>

    <?php if ($success) { ?>
      <div class="receipt">
        <h3>Order received</h3>
        <p><?php echo $orderId; ?> &middot; <?php echo $orderDate; ?>, <?php echo $orderTime; ?> WIB</p>
        <table>
          <tr><th>Name</th><td><?php echo safe($name); ?></td></tr>
          <tr><th>Email</th><td><?php echo safe($email); ?></td></tr>
          <tr><th>Product</th><td><?php echo safe($product); ?></td></tr>
          <tr><th>Finish</th><td><?php echo safe($finish); ?></td></tr>
          <tr><th>Price per piece</th><td><?php echo rupiah($unitPrice); ?></td></tr>
          <tr><th>Quantity</th><td><?php echo $quantity; ?></td></tr>
          <tr><th>Subtotal</th><td><?php echo rupiah($subtotal); ?></td></tr>
          <tr><th>Discount</th><td><?php echo rupiah($discount); ?></td></tr>
          <tr class="total"><th>Final total</th><td><?php echo rupiah($finalTotal); ?></td></tr>
        </table>
        <a class="btn small" href="?#order">Create another order</a>
      </div>
    <?php } else { ?>
      <form class="order-form" action="index_eoudiaarchives.php#order" method="post">
        <label for="name">Full name</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="product">Product</label>
        <select id="product" name="product" required>
          <option value="">Choose a piece</option>
          <?php foreach ($available as $productName => $productPrice) { ?>
            <option value="<?php echo safe($productName); ?>"<?php echo $selected === $productName ? " selected" : ""; ?>>
              <?php echo safe($productName); ?> - <?php echo rupiah($productPrice); ?>
            </option>
          <?php } ?>
        </select>

        <label for="finish">Finish</label>
        <select id="finish" name="finish" required>
          <?php foreach ($finishes as $finishName => $extra) { ?>
            <option value="<?php echo $finishName; ?>"><?php echo $finishName; ?><?php echo $extra > 0 ? " (+" . rupiah($extra) . ")" : ""; ?></option>
          <?php } ?>
        </select>

        <label for="quantity">Quantity</label>
        <input type="number" id="quantity" name="quantity" min="1" value="1" required>

        <button type="submit">Calculate my order</button>
      </form>
    <?php } ?>
  </section>

  <!-- ABOUT -->
  <section id="about" class="about">
    <h2>About Eoudia Archive</h2>
    <div class="about-cols">
      <div>
        <p>Eoudia Archive started with a simple idea: jewelry should survive your daily life. Most affordable accessories fade, turn your skin green, or rust after a few weeks. We make ours from 316L stainless steel so they do not.</p>
        <p>Every piece is unisex, designed with clean architectural lines, and carries an archive code like a museum object. We want you to keep it for years.</p>
      </div>
      <div>
        <h3>Available finishes</h3>
        <ul>
          <?php foreach ($finishes as $finishName => $extra) { ?>
            <li><?php echo $finishName; ?></li>
          <?php } ?>
        </ul>
      </div>
    </div>
  </section>
</main>

<footer>
  <p class="wordmark">Eoudia Archive</p>
  <div>
    <p>&copy; 2026 Eoudia Archive. Stainless steel jewelry for everyday wear.</p>
    <p class="views">Total Page Views: <?php echo $views; ?></p>
  </div>
</footer>

</body>
</html>