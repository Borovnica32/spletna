<?php
    session_start();

    include "funkcije.php";
    PreveriPiskotek();

    if(isset($_GET['stran'])){
        if($_GET['stran']=="internacionala"){
            include 'template/internacionala.html.php';
        }else{
            if(($_GET['stran']=="registracija") && !isset($_SESSION['uporabnik'])){
                include "template/registracija.html.php";
            }else{
                if(($_GET['stran']=="prijava") && !(isset($_SESSION['uporabnik']))){
                    include "template/prijava.html.php";
                }else{
                    if(($_GET['stran']=="chat") && isset($_SESSION['uporabnik'])){//session is stet
                        include "template/chat.html.php";
                    }else{  
                        if(($_GET['stran']=="chat") && !isset($_SESSION['uporabnik'])){//session is not set
                            	include "template/prijava.html.php";
                        }else{
                            if(($_GET['stran']=="forum") && isset($_SESSION['uporabnik'])){
                                include "template/threads.html.php";
                            }else{
                                if(($_GET['stran']=="newthread") && isset($_SESSION['uporabnik'])){
                                    include "template/newthread.html.php";
                                }else{
                                    if($_GET['stran']=="thread" && isset($_GET['id'])){
                                        include "template/thread.html.php";
                                    }else{
                                        if(($_GET['stran']=="admin_main") && $_SESSION['rang']==1){
                                            if(isset($_GET['kategorija'])){
                                                if($_GET['kategorija']=="dodajTorrent" && $_GET['stran']=="admin_main"){
                                                    include "template/Admin/torrentAdmin.html.php";
                                                }else{
                                                    if($_GET['kategorija']=="uporabniki" && $_GET['stran']=="admin_main"){
                                                        include "template/Admin/baza.html.php";
                                                    }else{
                                                        if($_GET['kategorija']=="seznamTorrentov" && $_GET['stran']=="admin_main"){
                                                            include "template/Admin/seznamTorrentov.html.php";
                                                        }else{
                                                            if($_GET['kategorija']=="urediTorrent" && $_GET['stran']=="admin_main" && isset($_GET['id'])){
                                                                include "template/Admin/urediTorrent.html.php";
                                                            }else{
                                                                if($_GET['kategorija']=="groups" && $_GET['stran']=="admin_main"){
                                                                    include "template/Admin/groupPolicy.html.php";
                                                                }else{
                                                                    if($_GET['kategorija']=="mojitiketi" && $_GET['stran']=="admin_main"){
                                                                        include "template/Admin/seznamTiketov.html.php";
                                                                    }else{
                                                                        if($_GET['kategorija']=="tiket" && $_GET['stran']=="admin_main" && isset($_GET['id'])){
                                                                            include "template/Admin/tiket.html.php";
                                                                        }else{
                                                                            include "template/napaka.html.php";
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }else{
                                                include "template/Admin/admin_main.html.php";
                                            }
                                        }else{
                                            if(($_GET['stran']=="odjava") && (isset($_SESSION['uporabnik']))){
                                                odjava();
                                            }else{
                                                if(($_GET['stran']=="profil") && (isset($_SESSION['uporabnik']))){
                                                    if(isset($_GET['kategorija'])){
                                                        if($_GET['kategorija']=="sprememba" && $_GET['stran']=="profil"){
                                                            include "template/spremembagesla.html.php";
                                                        }else{
                                                            if($_GET['stran']=="profil" && $_GET['kategorija']=="changeusrn"){
                                                                include "template/sprememba-usrn.html.php";
                                                            }else{
                                                                if($_GET['stran']=="profil" && $_GET['kategorija']=="userinfo"){
                                                                    include "template/userinfo.html.php";
                                                                }else{
                                                                    if($_GET['stran']=="profil" && $_GET['kategorija']=="newticket"){
                                                                        include "template/tiket.html.php";
                                                                    }else{
                                                                        if($_GET['stran']=="profil" && $_GET['kategorija']=="mytickets"){
                                                                            include "template/mytickets.html.php";
                                                                        }else{
                                                                            if($_GET['stran']=="profil" && $_GET['kategorija']=="tiketinfo"){
                                                                                include "template/tiketuser.html.php";
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }else{
                                                        include "template/profil.html.php";
                                                    }
                                                }else{
                                                    if($_GET['stran']=="torrent"){
                                                        include "template/torrent.html.php";
                                                    }else{
                                                        if(($_GET['stran']=="mod_main") && $_SESSION['rang']==2){
                                                            if(isset($_GET['kategorija'])){
                                                                if($_GET['kategorija']=="dodajTorrent" && $_GET['stran']=="mod_main"){
                                                                    include "template/Mod/torrentMod.html.php";
                                                                }else{
                                                                    if($_GET['kategorija']=="seznamTorrentov" && $_GET['stran']=="mod_main"){
                                                                        include "template/Mod/seznamTorrentov.html.php";
                                                                    }else{
                                                                        if($_GET['kategorija']=="urediTorrent" && $_GET['stran']=="mod_main" && isset($_GET['id'])){
                                                                            include "template/Mod/urediTorrent.html.php";
                                                                        }else{
                                                                            if($_GET['kategorija']=="mojitiketi" && $_GET['stran']=="mod_main"){
                                                                                include "template/Mod/seznamTiketov.html.php";
                                                                            }else{
                                                                                if($_GET['kategorija']=="tiket" && $_GET['stran']=="mod_main" && isset($_GET['id'])){
                                                                                    include "template/Mod/tiket.html.php";
                                                                                }else{
                                                                                    include "template/napaka.html.php";
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }else{
                                                                include "template/Mod/mod_main.html.php";
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    } 
                }
            }
        }  
    }else{
        include "template/napaka.html.php";
    }
?>
