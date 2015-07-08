<?php
if (isset($_SESSION))
{
    unset($_SESSION);
    session_unset();
    session_destroy();
}

?>
<h2>Logout</h2>
<p>Correctly logged out.</p>
<a href='.?page=login'>Log in on another session</a>