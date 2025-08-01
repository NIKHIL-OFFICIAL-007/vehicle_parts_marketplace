<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'buyer') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Batteries - AutoParts</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background: #f1f3f6;
    }

    .navbar {
      background: #2874f0;
      color: white;
      padding: 10px 30px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .navbar h1 {
      margin: 0;
      font-size: 24px;
    }

    .navbar a.logout {
      background: #e74c3c;
      padding: 8px 12px;
      color: white;
      text-decoration: none;
      border-radius: 4px;
      font-size: 14px;
    }

    .navbar a.logout:hover {
      background: #c0392b;
    }

    .main {
      padding: 20px;
      display: grid;
      grid-template-columns: 3fr 1fr;
      gap: 20px;
    }

    .section-title {
      font-size: 22px;
      margin-bottom: 20px;
      font-weight: 500;
      color: #333;
    }

    .products-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
      gap: 20px;
    }

    .product-card {
      background: white;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
      transition: transform 0.2s ease;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .product-card:hover {
      transform: translateY(-5px);
    }

    .product-card img {
      width: 100%;
      height: 160px;
      object-fit: contain;
      margin-bottom: 12px;
    }

    .product-card h3 {
      margin: 0;
      font-size: 16px;
      color: #222;
    }

    .product-card .brand {
      font-size: 13px;
      color: #777;
      margin-top: 4px;
    }

    .product-card .rating {
      background: #388e3c;
      color: white;
      font-size: 12px;
      padding: 2px 6px;
      border-radius: 4px;
      display: inline-block;
      margin-top: 8px;
    }

    .product-card .price {
      margin-top: 8px;
      font-size: 16px;
      font-weight: bold;
      color: #388e3c;
    }

    .product-card button {
      margin-top: 12px;
      background: #ff9f00;
      color: white;
      border: none;
      padding: 10px;
      border-radius: 4px;
      cursor: pointer;
      font-size: 14px;
    }

    .product-card button:hover {
      background: #fb8c00;
    }

    .filter-panel {
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 0 8px rgba(0,0,0,0.1);
    }

    .filter-panel h3 {
      font-size: 18px;
      margin-bottom: 15px;
      color: #333;
    }

    .filter-panel label {
      font-size: 14px;
      margin-top: 10px;
      display: block;
      color: #444;
    }

    .filter-panel select {
      width: 100%;
      padding: 8px;
      border-radius: 4px;
      margin-top: 5px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    @media (max-width: 900px) {
      .main {
        grid-template-columns: 1fr;
      }

      .filter-panel {
        order: -1;
        margin-bottom: 20px;
      }
    }
  </style>
</head>
<body>

  <!-- Navbar -->
  <div class="navbar">
    <h1>AutoParts</h1>
    <a href="buyer.php" class="logout"><i class="fa fa-arrow-left"></i> Back</a>
  </div>

  <!-- Main Content -->
  <div class="main">

    <!-- Products Section -->
    <div>
      <div class="section-title">🔋 Battery Parts</div>
      <div class="products-grid">

        <!-- Product Cards -->
        <div class="product-card">
          <img src="../images/battery.jpg" alt="battery" />
          <h3>Amaron Car Battery</h3>
          <div class="brand">Brand: Amaron</div>
          <div class="rating">4.6 ★</div>
          <div class="price">₹3,200</div>
          <button>Add to Cart</button>
        </div>

        <div class="product-card">
          <img src="images/battery.jpg" alt="Exide Battery">
          <h3>Exide Matrix Battery</h3>
          <div class="brand">Brand: Exide</div>
          <div class="rating">4.4 ★</div>
          <div class="price">₹3,000</div>
          <button>Add to Cart</button>
        </div>

      </div>
    </div>

    <!-- Filter Panel -->
    <div class="filter-panel">
      <h3>Filter Products</h3>

      <label for="vehicle">Vehicle Brand</label>
      <select id="vehicle" name="vehicle" onchange="updateModels()">
        <option value="">-- Select Brand --</option>
        <option value="maruti">Maruti</option>
        <option value="skoda">Skoda</option>
        <option value="honda">Honda</option>
        <option value="tata">Tata</option>
      </select>

      <label for="model">Model</label>
      <select id="model" name="model">
        <option>-- Select Model --</option>
      </select>

      <label for="year">Year</label>
      <select id="year" name="year">
        <option>-- Select Year --</option>
        <option>2020</option>
        <option>2021</option>
        <option>2022</option>
        <option>2023</option>
        <option>2024</option>
      </select>

      <label for="brand">Product Brand</label>
      <select id="brand" name="brand">
        <option>-- Select Brand --</option>
        <option>Amaron</option>
        <option>Exide</option>
        <option>SF Sonic</option>
      </select>

      <label for="rating">Minimum Rating</label>
      <select id="rating" name="rating">
        <option>-- Select Rating --</option>
        <option>4.0 ★ and above</option>
        <option>4.5 ★ and above</option>
      </select>

    </div>

  </div>

  <!-- JavaScript for dynamic model change -->
  <script>
    const vehicleModels = {
      maruti: ["Swift", "Baleno", "Dzire", "WagonR"],
      skoda: ["Rapid", "Slavia", "Octavia", "Kushaq"],
      honda: ["City", "Amaze", "Jazz", "WR-V"],
      tata: ["Nexon", "Harrier", "Tiago", "Altroz"]
    };

    function updateModels() {
      const vehicleSelect = document.getElementById("vehicle");
      const modelSelect = document.getElementById("model");
      const selectedBrand = vehicleSelect.value;

      // Clear previous models
      modelSelect.innerHTML = '<option>-- Select Model --</option>';

      if (vehicleModels[selectedBrand]) {
        vehicleModels[selectedBrand].forEach(model => {
          const option = document.createElement("option");
          option.value = model;
          option.textContent = model;
          modelSelect.appendChild(option);
        });
      }
    }
  </script>

</body>
</html>
