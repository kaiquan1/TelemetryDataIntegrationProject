<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Page</title>
  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
      background-color: #2c3e50;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .box-form {
      display: flex;
      width: 800px;
      height: 400px;
      background-color: #34495e;
      border-radius: 10px;
      overflow: hidden;
    }

    .left {
      flex: 1;
      background-color: #2c3e50;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
    }

    .overlay {
      text-align: left;
    }

    .overlay h2 {
      margin-bottom: 20px;
    }

    .overlay ul {
      list-style: none;
      padding: 0;
    }

    .overlay ul li {
      margin-bottom: 10px;
    }

    .right {
      flex: 1;
      background-color: #7f8c8d;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      padding: 20px;
    }

    .right h5 {
      margin-bottom: 20px;
      color: white;
    }

    .inputs {
      width: 100%;
      max-width: 300px;
    }

    .inputs input {
      width: 100%;
      padding: 10px;
      margin-bottom: 10px;
      border: none;
      border-radius: 5px;
    }

    button {
      padding: 10px 20px;
      border: none;
      border-radius: 5px;
      background-color: #1abc9c;
      color: white;
      cursor: pointer;
      width: 100%;
      max-width: 300px;
    }

    button:hover {
      background-color: #16a085;
    }

    #errorMessage {
      color: red;
      display: none;
    }
  </style>
</head>
<body>
  <div class="box-form">
    <div class="left">
      <div class="overlay">
        <h2>Welcome Comprehensive Meter Reading Platform</h2>
        <ul>
          <li>Add Residential Water Module</li>
          <li>Add Nonresidential Water Module</li>
          <li>Add Wireless Lora Water Meter</li>
        </ul>
      </div>
    </div>
    <div class="right">
      <h5>Login</h5>
      <form id="loginForm">
        <div class="inputs">
          <input type="text" id="username" placeholder="username" required>
          <br>
          <input type="password" id="password" placeholder="password" required>
        </div>
        <br>
        <button type="submit">Login</button>
      </form>
      <p id="errorMessage">Invalid username or password.</p>
    </div>
  </div>
  
  <script>
    // Simulate reading user data from a text file
    let usersData = ``;

    // Fetch the user data from the text file
	fetch('users.txt')
  		.then(response => response.text())
  		.then(data => {
    		usersData = data;
		})
  		.catch(error => console.error('Error fetching user data:', error));

    function validateUser(username, password) {
      const users = usersData.trim().split('\r\n').map(line => line.split(':'));
	  console.log(users);
      for (let [user, pass] of users) {
        if (user === username && pass === password) {
          return true;
        }
      }
      return false;
    }

    document.getElementById('loginForm').addEventListener('submit', function(event) {
      event.preventDefault();
      const username = document.getElementById('username').value;
      const password = document.getElementById('password').value;

      if (validateUser(username, password)) {
        alert('Login successful!');
        // Redirect to another page on successful login
        window.location.href = 'dashboard.php';
      } else {
        document.getElementById('errorMessage').style.display = 'block';
		console.log(users);
      }
    });
  </script>
</body>
</html>
