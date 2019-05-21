        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
<?php foreach ($categories as $key => $category): ?>
                    <li<?php echo $category['active']; ?>><a href="<?php echo $key; ?>"><?php echo $category['label']; ?></a></li>
<?php endforeach; ?>
                </ul>
                <ul class="nav navbar-nav navbar-right">
                    <li style="margin-right:1em;"><img src="assets/images/Rotunde.png" height="50" alt="Logo HAB"/></li>
                </ul>
            </div>
        </nav>
