<?php
	$link = mysqli_connect('localhost', 'root', '');
	mysqli_query($link, "CREATE DATABASE onelangchallenge");
	mysqli_close($link);

	$link = mysqli_connect('localhost', 'root', '', 'onelangchallenge');
	mysqli_query($link, 'CREATE TABLE list (
		id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
		content TEXT,
		is_checked BOOLEAN DEFAULT FALSE)');


	function get_tasks() {
		$result = mysqli_query($GLOBALS['link'], 'SELECT * FROM list ORDER BY is_checked, id DESC');
		$tasks = array();

		for ($i = 0; $i < mysqli_num_rows($result); $i++) {
			$tasks[] = mysqli_fetch_assoc($result);
		}

		return $tasks;
	}

	function add_task($content) {
		mysqli_query($GLOBALS['link'], 'INSERT INTO list (content) VALUES ("'.$content.'")');
	}

	function toggle_task($id) {
		mysqli_query($GLOBALS['link'], 'UPDATE list SET is_checked=NOT is_checked WHERE id='.$id);
	}

	function delete_task($id) {
		mysqli_query($GLOBALS['link'], 'DELETE FROM list WHERE id='.$id);
	}

	switch (isset($_GET['action']) ? $_GET['action'] : null) {

		case 'add-task':
			add_task($_POST['content']);
			header('Location: index.php');
			break;

		case 'toggle-task':
			toggle_task($_GET['id']);
			header('Location: index.php');
			break;

		case 'delete-task':
			delete_task($_GET['id']);
			header('Location: index.php');
			break;
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Todo list — OneLangChallenge</title>
		<style>
			body {
				font-family: sans-serif;	
			}

			form,
			section {
				max-width: 300px;
				margin: 20px auto;
			}

			form {
				display: flex;
			}

			form > input {
				flex-grow: 2;
				margin-right: 5px;
			}

			section:empty::after {
				display: block;
				content: "No tasks yet";

				text-align: center;
			}

			article + article {
				margin-top: 20px;
			}

			article > a {
				float: right;
			}

			.checked {
				color: grey;
				text-decoration: line-through;
			}

			.grey {
				color: grey;
			}
		</style>
	</head>
	<body>
		<form action="index.php?action=add-task" method="POST">
			<input type="text" name="content" placeholder="Add new task" autocomplete="off" required autofocus>
			<button>Add</button>
		</form>
		<section><?php foreach(get_tasks() as $task): ?>
				<?php if ($task['is_checked']): ?>
					<article id="<?=$task['id'] ?>" class="checked">
						<input type="checkbox" checked>
						<span>
							<?php echo $task['content'] ?>
						</span>
						<a href="index.php?action=delete-task&id=<?=$task['id']?>" class="grey">Remove</a>
					</article>
				<?php else: ?>
					<article id="<?=$task['id'] ?>">
						<input type="checkbox">
						<span>
							<?php echo $task['content'] ?>
						</span>
						<a href="index.php?action=delete-task&id=<?=$task['id']?>">Remove</a>
					</article>
				<?php endif ?>
			<?php endforeach ?></section>
		<script>
			'use strict';

			document.querySelectorAll('input[type*="checkbox"]').forEach(function (checkbox) {
				checkbox.addEventListener('change', function (event) {
					event.target.parentNode.classList.toggle('checked');
					window.location.href = "index.php?action=toggle-task&id=" + event.target.parentNode.id;
				});
			});
		</script>
	</body>
</html>
