<?php
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Origin, Content-Type');
require 'controllers/blogs_controller.php';

$blogs = new Blogs();
switch ($_SERVER['REQUEST_METHOD']) {
    case 'GET':
        if (isset($_GET['id'])) {
            $post = $blogs->retrieveOneBlog($_GET['id']);
            $contents = file_get_contents($post->file_path);
            $post->file_path = json_decode($contents);
            echo json_encode($post);
        } else {
            $posts = $blogs->retrieveBlogs();

            foreach ($posts as $post) {
                $contents = file_get_contents($post->file_path);
                $post->file_path = json_decode($contents);
            }
            echo json_encode($posts);
        }
        break;
    case 'POST':
        if (isset($_POST['id'])) {
            $data = json_decode($_POST['data']);
            $id = $_POST['id'];
            $title = $data->title;
            $content = $data->content;
            $path = $data->path;
            $targetPath = "";
            if ($_FILES['image']) {
                $image = $_FILES['image'];
                if ($image['error'] === UPLOAD_ERR_OK) {
                    $targetPath = "/images/blogs/" . $image['name'];
                    $content = json_encode($content);

                    file_put_contents('./blogs/' . $path . '.json', $content);

                    if ($blogs->updateBlog($id, $title, $targetPath)) {
                        $targetPath = "." . $targetPath;
                        if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                            echo "You have successfully updated a blog!";
                        } else {
                            echo "Error file upload!";
                        }
                    } else {
                        echo "Error in saving";
                    }
                }
            } else {
                $content = json_encode($content);

                file_put_contents('./blogs/' . $path . '.json', $content);

                if ($blogs->updateBlog($id, $title)) {
                    echo "You have successfully updated a blog!";
                } else {
                    echo "Error in saving";
                }
            }
        } else {
            if (isset($_POST['data'])) {
                $data = json_decode($_POST['data']);
                $title = $data->title;
                $content = $data->content;

                $content = json_encode($content);

                $id =  uniqid() . date("YmdHis"); //6635fec2496ac

                $filePath = './blogs/' . $id . '.json';

                if ($_FILES['image']) {
                    $image = $_FILES['image'];
                    $targetPath = "/images/blogs/" . $image['name'];

                    file_put_contents($filePath, $content);

                    if ($blogs->insertBlog($title, $id, $filePath, $targetPath)) {
                        $targetPath = "." . $targetPath;
                        if (move_uploaded_file($image['tmp_name'], $targetPath)) {
                            echo "You have successfully created a blog!";
                        } else {
                            echo "Error file upload!";
                        }
                    } else {
                        echo "Error in saving";
                    }
                }
            }
        }
        break;
    case 'PUT':
        $info = json_decode(file_get_contents('php://input'));
        if ($blogs->updateBlog($info->id, $info->title, $info->images, $info->content)) {
            echo "Blog updated!";
        } else {
            echo "Some content has no changes.";
        }
        break;
    case 'DELETE':
        if ($blogs->deleteBlog($_GET["id"])) {
            echo "Blog deleted!";
        }
        break;
}
