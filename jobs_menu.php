<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$database = "351delta";

// Create a connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['Admin_Email'] {
	$can_create = false;
	$can_delete_all = true;
	$can_view = true;
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Job Dashboard</title>
	</head>
	<body>
		<h2>Job Postings</h2>

		<?php if ($can_create): ?>
			<h3>Create Job Post</h3>
			<form method="POST" action="create_post.php">
				<input type="text" name="title" placeholder="Job Title" required><br>
				<textarea name="description" placeholder="Job Description" required></textarea><br>
				<button type="submit">Post Job</button>
			</form>
			<hr>
		<?php endif; ?>

		<h3>Available Jobs</h3>
		<ul>
			<?php foreach ($job_posts as $post): ?>
				<li>
					<strong><?= htmlspecialchars($post['title']) ?></strong><br>
					<?= htmlspecialchars($post['description']) ?><br>
					<small>Posted by: <?= htmlspecialchars($post['posted_by']) ?> (<?= $post['user_type'] ?>)</small><br>

					<?php
					$can_delete_own = $post['posted_by'] === $_SESSION['username'] && $can_create;
					if ($can_delete_all || $can_delete_own):
					?>
						<form method="POST" action="delete_post.php" style="display:inline;">
							<input type="hidden" name="post_id" value="<?= $post['id'] ?>">
							<button type="submit">Delete</button>
						</form>
					<?php endif; ?>
				</li>
				<hr>
			<?php endforeach; ?>
		</ul>
	</body>
	</html>
	<?php
}

if (!isset($_SESSION['Alumni_Email'] {
	$can_create = true;
	$can_delete_all = false;
	$can_view = true;
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Job Dashboard</title>
	</head>
	<body>
		<h2>Job Postings</h2>

		<?php if ($can_create): ?>
			<h3>Create Job Post</h3>
			<form method="POST" action="create_post.php">
				<input type="text" name="title" placeholder="Job Title" required><br>
				<textarea name="description" placeholder="Job Description" required></textarea><br>
				<button type="submit">Post Job</button>
			</form>
			<hr>
		<?php endif; ?>

		<h3>Available Jobs</h3>
		<ul>
			<?php foreach ($job_posts as $post): ?>
				<li>
					<strong><?= htmlspecialchars($post['title']) ?></strong><br>
					<?= htmlspecialchars($post['description']) ?><br>
					<small>Posted by: <?= htmlspecialchars($post['posted_by']) ?> (<?= $post['user_type'] ?>)</small><br>

					<?php
					$can_delete_own = $post['posted_by'] === $_SESSION['username'] && $can_create;
					if ($can_delete_all || $can_delete_own):
					?>
						<form method="POST" action="delete_post.php" style="display:inline;">
							<input type="hidden" name="post_id" value="<?= $post['id'] ?>">
							<button type="submit">Delete</button>
						</form>
					<?php endif; ?>
				</li>
				<hr>
			<?php endforeach; ?>
		</ul>
	</body>
	</html>
	<?php
}

if (!isset($_SESSION['Professor_Email'] {
	$can_create = true;
	$can_delete_all = false;
	$can_view = true;
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Job Dashboard</title>
	</head>
	<body>
		<h2>Job Postings</h2>

		<?php if ($can_create): ?>
			<h3>Create Job Post</h3>
			<form method="POST" action="create_post.php">
				<input type="text" name="title" placeholder="Job Title" required><br>
				<textarea name="description" placeholder="Job Description" required></textarea><br>
				<button type="submit">Post Job</button>
			</form>
			<hr>
		<?php endif; ?>

		<h3>Available Jobs</h3>
		<ul>
			<?php foreach ($job_posts as $post): ?>
				<li>
					<strong><?= htmlspecialchars($post['title']) ?></strong><br>
					<?= htmlspecialchars($post['description']) ?><br>
					<small>Posted by: <?= htmlspecialchars($post['posted_by']) ?> (<?= $post['user_type'] ?>)</small><br>

					<?php
					$can_delete_own = $post['posted_by'] === $_SESSION['username'] && $can_create;
					if ($can_delete_all || $can_delete_own):
					?>
						<form method="POST" action="delete_post.php" style="display:inline;">
							<input type="hidden" name="post_id" value="<?= $post['id'] ?>">
							<button type="submit">Delete</button>
						</form>
					<?php endif; ?>
				</li>
				<hr>
			<?php endforeach; ?>
		</ul>
	</body>
	</html>
	<?php
}
if (!isset($_SESSION['Student_Email'] {
	$can_create = false;
	$can_delete_all = false;
	$can_view = true;
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Job Dashboard</title>
	</head>
	<body>
		<h2>Job Postings</h2>

		<?php if ($can_create): ?>
			<h3>Create Job Post</h3>
			<form method="POST" action="create_post.php">
				<input type="text" name="title" placeholder="Job Title" required><br>
				<textarea name="description" placeholder="Job Description" required></textarea><br>
				<button type="submit">Post Job</button>
			</form>
			<hr>
		<?php endif; ?>

		<h3>Available Jobs</h3>
		<ul>
			<?php foreach ($job_posts as $post): ?>
				<li>
					<strong><?= htmlspecialchars($post['title']) ?></strong><br>
					<?= htmlspecialchars($post['description']) ?><br>
					<small>Posted by: <?= htmlspecialchars($post['posted_by']) ?> (<?= $post['user_type'] ?>)</small><br>

					<?php
					$can_delete_own = $post['posted_by'] === $_SESSION['username'] && $can_create;
					if ($can_delete_all || $can_delete_own):
					?>
						<form method="POST" action="delete_post.php" style="display:inline;">
							<input type="hidden" name="post_id" value="<?= $post['id'] ?>">
							<button type="submit">Delete</button>
						</form>
					<?php endif; ?>
				</li>
				<hr>
			<?php endforeach; ?>
		</ul>
	</body>
	</html>
	<?php
}

if (!isset($_SESSION['Employer_Email'] {
	$can_create = true;
	$can_delete_all = false;
	$can_view = true;
	?>
	<!DOCTYPE html>
	<html>
	<head>
		<title>Job Dashboard</title>
	</head>
	<body>
		<h2>Job Postings</h2>

		<?php if ($can_create): ?>
			<h3>Create Job Post</h3>
			<form method="POST" action="create_post.php">
				<input type="text" name="title" placeholder="Job Title" required><br>
				<textarea name="description" placeholder="Job Description" required></textarea><br>
				<button type="submit">Post Job</button>
			</form>
			<hr>
		<?php endif; ?>

		<h3>Available Jobs</h3>
		<ul>
			<?php foreach ($job_posts as $post): ?>
				<li>
					<strong><?= htmlspecialchars($post['title']) ?></strong><br>
					<?= htmlspecialchars($post['description']) ?><br>
					<small>Posted by: <?= htmlspecialchars($post['posted_by']) ?> (<?= $post['user_type'] ?>)</small><br>

					<?php
					$can_delete_own = $post['posted_by'] === $_SESSION['username'] && $can_create;
					if ($can_delete_all || $can_delete_own):
					?>
						<form method="POST" action="delete_post.php" style="display:inline;">
							<input type="hidden" name="post_id" value="<?= $post['id'] ?>">
							<button type="submit">Delete</button>
						</form>
					<?php endif; ?>
				</li>
				<hr>
			<?php endforeach; ?>
		</ul>
	</body>
	</html>
	<?php
}

// if admin clicks delete job button: confirm if admin wants to delete job then remove from database and refresh page
// if student clicks apply: go to job application page
?>