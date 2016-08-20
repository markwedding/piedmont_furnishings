<?php
  ob_start();
  session_start();
  include("components.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
   "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php html_head("Sales Agent Home"); ?>
</head>
<body>
  <?php
  # Add the navbar
  html_navbar("agent", "home.php");
  ?>

  <div class="container home-page">
    <h2>
      Sales Order Analysis and Reporting System
    </h2>
    <hr></hr>
    <p>
      <strong>Sales Agent:</strong> <?php echo $_SESSION['fname'] . " " . $_SESSION['lname'];?>
    </p>
    <p>
      <strong>Project Description:</strong> Piedmont Furnishings is a growing hand-made furniture company based out of High Point, North Carolina. It offers 16 different products in 3 different general categories. While a large number of the company’s sales come from walk-in customers at the store in High Point, it also depends on sales agents who work in different regions outside of North Carolina. There are six different regions that these agents operate in: northwest, southwest, northeast, midwest, southeast, and international.
    </p>
    <p>
      Up until this point, the company has relied on paper documents to conduct their business. Sales agents fill out physical order forms and customer forms, and must mail them to the store in High Point. In addition, managers rely upon manually constructed reports to analyze the company. The Sales Order Analysis and Reporting System is a web-based support system that will allow all of these forms and reports to be done through the web. Rather than mailing forms to the physical store, sales agents will be able to simply logon to the system through a web browser and fill out electronic forms. Managers will be able to generate reports based upon the data that is entered into the system.
    </p>
  </div>

</body>
</html>
