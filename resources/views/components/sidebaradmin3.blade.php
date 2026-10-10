

<div class="leftside-menu">

    <!-- Brand Logo Light -->
    <a href="index.html" class="logo logo-light">
        <span class="logo-lg">
            <img src="{{ asset('/public/assets/images/logo.png') }}" alt="logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('/public/assets/images/logo-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Brand Logo Dark -->
    <a href="index.html" class="logo logo-dark">
        <span class="logo-lg">
            <img src="{{ asset('/public/assets/images/logo-dark.png') }}" alt="dark logo">
        </span>
        <span class="logo-sm">
            <img src="{{ asset('/public/assets/images/logo-dark-sm.png') }}" alt="small logo">
        </span>
    </a>

    <!-- Sidebar Hover Menu Toggle Button -->
    <div class="button-sm-hover" data-bs-toggle="tooltip" data-bs-placement="right" title="Show Full Sidebar">
        <i class="ri-checkbox-blank-circle-line align-middle"></i>
    </div>

    <!-- Full Sidebar Menu Close Button -->
    <div class="button-close-fullsidebar">
        <i class="ri-close-fill align-middle"></i>
    </div>

    <!-- Sidebar -->
    <div class="h-100" id="leftside-menu-container" data-simplebar>
        <!-- Leftbar User -->
        <div class="leftbar-user">
            <a href="pages-profile.html">
                <img src="{{ asset('/public/assets/images/users/avatar-1.jpg') }}" alt="user-image"
                    height="42" class="rounded-circle shadow-sm">
                <span class="leftbar-user-name mt-2">ProBox</span>
            </a>
        </div>

        <!--- Sidemenu -->
        <ul class="side-nav">

            <!--<li class="side-nav-title">Navigation</li>-->

            <!--<li class="side-nav-item">-->
            <!--    <a href="{{ route('dashboard.admin') }}" class="side-nav-link">-->
            <!--        <i class="uil-calender"></i>-->
            <!--        <span> Dashboard </span>-->
            <!--    </a>-->
            <!--</li>-->

            <li class="side-nav-title">Apps</li>


   
            
            

    

           


      

         



            
            
            
            
           
            
            
            

       

            
            
         
            
            <li class="side-nav-item">
                <a data-bs-toggle="collapse" href="#sidebarRegistrationForm" aria-expanded="false"
                    aria-controls="sidebarRegistrationForm" class="side-nav-link">
                    <i class="uil-schedule""></i>
                    <span>Set-up</span>
                    <span class="menu-arrow"></span>
                </a>
                <div class="collapse" id="sidebarRegistrationForm">
                    <ul class="side-nav-second-level">
                    
                        <li>
                            <a href="{{ route('company_reg.index') }}">Register Companies</a>
                        </li>
                        
                    </ul>
                </div>
            </li>


            
            
            
     
            
            

        


        </ul>
        <!--- End Sidemenu -->

        <div class="clearfix"></div>
    </div>
</div>