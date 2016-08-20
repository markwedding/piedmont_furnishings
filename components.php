<?php
  # Creates the head element with all links to style sheets and scripts
  function html_head($page_title) { ?>
      <title><?php echo $page_title;?></title>
      <link href="http://bootswatch.com/lumen/bootstrap.min.css" rel="stylesheet" type="text/css"/>
      <link href="style.css" rel="stylesheet" type="text/css"/>
      <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
      <script src="https://bootswatch.com/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
      <script type="text/javascript" src="script.js"></script>
  <?php }

  # Creates the navbar
  function html_navbar($type, $homelink) { ?>
    <nav class="navbar navbar-default">
      <div class="container">
        <div class="navbar-header">
          <?php if($type!=='plain') {?>
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
              <span class="sr-only">Toggle navigation</span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
              <span class="icon-bar"></span>
            </button>
          <?php }?>
          <a class="navbar-brand" href="<?php echo $homelink;?>">
            <strong>Piedmont Furnishings</strong>
          </a>
        </div>

        <?php if($type!=='plain') {?>
        <div class="collapse navbar-collapse" id="navbar-collapse-1">
          <ul class="nav navbar-nav">
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Orders <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="orderform.php?action=create">Create</a></li>
                <li><a href="orderform.php?action=edit">Edit</a></li>
              </ul>
            </li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Customers <span class="caret"></span></a>
              <ul class="dropdown-menu" role="menu">
                <li><a href="customerform.php?action=create">Create</a></li>
                <li><a href="customerform.php?action=edit">Edit</a></li>
              </ul>
            </li>
            <?php if($type=='manager') {?>
            <li><a href="sales.php">Itemized Sales Report</a></li>
            <?php }?>
          </ul>
          <ul class="nav navbar-nav navbar-right">
            <li><a href="logout.php">Log Out</a></li>
          </ul>
        </div>
        <?php }?>
      </div>
    </nav>
  <?php }


?>
