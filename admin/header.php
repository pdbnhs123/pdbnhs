<div class="content-area">
    <div class="header">
       <div class="user-menu">
           <button class="dark-toggle" title="Toggle Dark Mode"><i class="fas fa-moon"></i></button>
           <div class="user-btn">
               <i class="fas fa-user-circle"></i> <span><?= htmlspecialchars($username) ?></span> </div>
           <div class="dropdown">
               <button onclick="location.href='settings.php'"><i class="fas fa-cog"></i> Settings</button>
               <button onclick="location.href='logout.php'"><i class="fas fa-sign-out-alt"></i> Logout</button>
           </div>
       </div>
        <div class="mobile-menu-toggle"><i class="fas fa-bars"></i></div>
   </div>