<?php
require_once 'controller.php';

class Blogs extends Controller
{
    function retrieveBlogs()
    {
        $this->setStatement("SELECT * FROM blogs WHERE `status` = 1");
        $this->statement->execute();
        return $this->statement->fetchAll();
    }
    function retrieveOneBlog($id)
    {
        $this->setStatement("SELECT * FROM blogs WHERE blog_id = ? AND `status` = 1");
        $this->statement->execute([$id]);
        return $this->statement->fetch();
    }
    function insertBlog($title, $file_id, $path, $image)
    {
        $this->setStatement("INSERT INTO `blogs`(`title`,`image`, `file_id`, `file_path`, `status`) VALUES (?,?,?,?,1)");
        return $this->statement->execute([$title, $image, $file_id, $path]);
    }
    function updateBlog($id, $title, $image = null)
    {
        if ($image) {
            $this->setStatement("UPDATE `blogs` SET `title` = ?, `image` = ? WHERE blog_id = ? AND `status` = 1");
            return $this->statement->execute([$title, $image, $id]);
        } else {
            $this->setStatement("UPDATE `blogs` SET `title` = ? WHERE blog_id = ? AND `status` = 1");
            return $this->statement->execute([$title, $id]);
        }
    }
    function deleteBlog($id)
    {
        $this->setStatement("UPDATE `blogs` SET `status` = 0 WHERE blog_id = ?");
        return $this->statement->execute([$id]);
    }
}
