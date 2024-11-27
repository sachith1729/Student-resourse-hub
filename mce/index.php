<?php
session_start();

// Redirect to login page if the user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Database connection settings
$host = "localhost";
$dbname = "StudentResourceHub";
$username = "root";
$password = "";

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Handle AJAX request to fetch resources by department
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['department'])) {
    $department = $_GET['department'];

    try {
        $stmt = $conn->prepare("SELECT semester, topic FROM resources WHERE department = :department ORDER BY semester, topic");
        $stmt->bindParam(':department', $department);
        $stmt->execute();
        $resources = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$resources) {
            echo json_encode(["message" => "No resources found for the selected department."]);
            exit;
        }

        // Organize resources by semester
        $result = [];
        foreach ($resources as $resource) {
            $semester = $resource['semester'];
            $topic = $resource['topic'];

            if (!isset($result[$semester])) {
                $result[$semester] = [];
            }
            $result[$semester][] = $topic;
        }

        echo json_encode($result);
        exit;
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(["error" => "Failed to fetch resources: " . $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Resource Hub</title>
    <style>
/* General Styles */
body {
    font-family: 'Arial', sans-serif;
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    color: #333;
}

header {
    text-align: center;
    padding: 20px;
    background: #fff;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
}

header h1 {
    margin: 0;
    color: #6e8efb;
    font-size: 2rem;
}

header p {
    margin: 5px 0;
    font-size: 1rem;
    color: #555;
}

/* Resource Section */
.resources {
    max-width: 800px;
    margin: 30px auto;
    background: #f9f9f9;
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
}

.departments {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}

.departments button {
    padding: 15px 20px;
    font-size: 1rem;
    color: #fff;
    background-color: #6e8efb;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.departments button:hover {
    transform: scale(1.1);
    box-shadow: 0 8px 15px rgba(110, 142, 251, 0.6);
}

#resourceContainer {
    margin-top: 20px;
}

/* Semester Blocks */
.semester-block {
    margin: 20px 0;
    padding: 15px;
    border-radius: 12px;
    background: linear-gradient(135deg, #6e8efb, #a777e3);
    color: #fff;
    animation: fadeIn 0.7s ease;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.semester-block h4 {
    margin-bottom: 10px;
    font-size: 1.2rem;
}

.semester-block ul {
    list-style: none;
    padding: 0;
}

.semester-block ul li {
    margin: 8px 0;
    padding: 8px 12px;
    background: #fff;
    color: #6e8efb;
    border-radius: 8px;
    font-size: 0.9rem;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.semester-block ul li:hover {
    transform: translateX(10px);
    background-color: #d4e2fc;
}

/* Logout Button */
.logout {
    position: absolute;
    top: 20px;
    right: 20px;
    padding: 10px 20px;
    background-color: #ff4d4d;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 1rem;
    cursor: pointer;
    transition: transform 0.3s ease, background-color 0.3s ease;
}

.logout:hover {
    background-color: #e60000;
    transform: scale(1.05);
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

    </style>
</head>
<body>
    <header>
        <h1>Welcome, <?php echo isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'Guest'; ?>!</h1>
        <p>Access all your academic resources in one place.</p>
        <form method="POST" action="logout.php">
            <button type="submit" class="logout">Logout</button>
        </form>
    </header>
    <main>
        <section class="resources">
            <h3>Academic Resources</h3>
            <p>Select your department to access categorized resources:</p>
            <div class="departments">
                <button onclick="loadResources('CS')">Computer Science (CS)</button>
                <button onclick="loadResources('IS')">Information Science (IS)</button>
                <button onclick="loadResources('ME')">Mechanical Engineering (ME)</button>
                <button onclick="loadResources('CSD')">Computer Science and Design (CSD)</button>
                <button onclick="loadResources('CSDS')">CS with Data Science (CSDS)</button>
                <button onclick="loadResources('AIML')">AI & ML (AIML)</button>
            </div>
            <div id="resourceContainer">
                <!-- Resources will be loaded dynamically here -->
            </div>
        </section>
    </main>
    <script>
        async function loadResources(department) {
            const resourceContainer = document.getElementById("resourceContainer");

            // Show a loading message while fetching
            resourceContainer.innerHTML = "<p>Loading resources...</p>";
            resourceContainer.style.display = "block";

            try {
                const response = await fetch(`index.php?department=${department}`);
                const data = await response.json();

                if (response.ok) {
                    // Clear previous content
                    resourceContainer.innerHTML = "";

                    // Populate resources
                    for (const semester in data) {
                        const semesterBlock = document.createElement("div");
                        semesterBlock.innerHTML = `<h4>${semester}</h4>`;
                        const topicList = document.createElement("ul");

                        data[semester].forEach((topic) => {
                            const listItem = document.createElement("li");
                            listItem.textContent = topic;
                            topicList.appendChild(listItem);
                        });

                        semesterBlock.appendChild(topicList);
                        resourceContainer.appendChild(semesterBlock);
                    }
                } else {
                    resourceContainer.innerHTML = `<p>${data.message || "No resources found."}</p>`;
                }
            } catch (error) {
                resourceContainer.innerHTML = `<p>Error loading resources. Please try again later.</p>`;
            }
        }
    </script>
</body>
</html>
