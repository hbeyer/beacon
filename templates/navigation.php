        <nav class="navbar navbar-inverse navbar-fixed-top">
            <div class="container-fluid">
                <ul class="nav navbar-nav">
<?php foreach ($categories as $key => $category): ?>
                    <li<?php echo $category['active']; ?>><a href="<?php echo $key; ?>"><?php echo $category['label']; ?></a></li>
<?php endforeach; ?>
                </ul>
                <form class="navbar-form navbar-left" action="index.php">
                    <div class="form-group">
                        <input type="text" pattern="[0-9X-]{9,10}" class="form-control" placeholder="GND" name="gnd">
                    </div>
                    <button type="submit" class="btn btn-default">Los</button>
                </form>
                <ul class="nav navbar-nav navbar-right">
                    <li style="margin-right:1em;"><img src="assets/images/icon.svg" height="50" alt="Logo HAB"/></li>
                </ul>
            </div>
        </nav>
