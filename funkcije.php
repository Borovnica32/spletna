<?php
    #Region ** Baza **
        //funkcija za vzpostavitev povezave do baze
        function open_database_connection(){
            $link=new mysqli("localhost", "borovnica", "210784", "zverina");
            $link->query("SET NAMES 'utf8'");
            return $link;
        }

        //Funkcija za zapiranje povezave do baze
        function close_database_connection($link){
            mysqli_close($link);
        }
    #End Regeon

    #Region ** Prijave **
        //Funkcija za prijavo
        function prijava() {
            if(isset($_POST['gumb'])) {
                $username = strip_tags(trim($_POST['username']));
                $password = $_POST['password'];
        
                if(empty($username) || empty($password)){
                    echo '<script type="text/javascript"> alert("You left empty fields in the form!") </script>';
                } else {
                    $link = open_database_connection();
                    $sql = "SELECT UPORABNIK, GESLO, ID_RANG, VALIDATOR, SELEKTOR, ZADNJAPRIJAVA FROM Blinker_IP WHERE UPORABNIK=?";
                    $params = array($username);
                    $rezultat = mysqli_execute_query($link, $sql, $params);
        
                    if($rezultat === false){
                        echo '<script type="text/javascript"> alert("An error occurred while processing your request.") </script>';
                    } else {
                        $vrstica = mysqli_fetch_row($rezultat);
        
                        if(empty($vrstica)){
                            echo '<script type="text/javascript"> alert("Wrong username!") </script>';
                        } else {
                            if(!password_verify($password, $vrstica[1])){
                                echo '<script type="text/javascript"> alert("Wrong password!") </script>';
                            } else {
                                if($vrstica[2] == 4){
                                    echo '<script type="text/javascript"> alert("Your account has been suspended!") </script>';
                                } else {
                                    $_SESSION['uporabnik'] = $username;
                                    $zadnjaprijava = date("Y-m-d H:i:s");
                                    $_SESSION['rang'] = $vrstica[2];
        
                                    if(isset($_POST['cookie'])){
                                        $selektor = $vrstica[4];
                                        $validator = $vrstica[3];
                                        $vrednost = $selektor . $validator;
                                        $cas = 3600;
                                        $potece = date("Y-m-d H:i:s", strtotime("+$cas sec"));
                                        setcookie("piskot", $vrednost, time() + $cas);
                                        $sql = "UPDATE Blinker_IP SET POTECE=?, ZADNJAPRIJAVA=? WHERE UPORABNIK=?";
                                        $params = array($potece, $zadnjaprijava, $username);
                                    } else {
                                        $sql = "UPDATE Blinker_IP SET ZADNJAPRIJAVA=? WHERE UPORABNIK=?";
                                        $params = array($zadnjaprijava, $username);
                                    }
        
                                    $success = mysqli_execute_query($link, $sql, $params);
                                    if($success === false){
                                        echo '<script type="text/javascript"> alert("An error occurred while updating user data.") </script>';
                                    } else {
                                        header("Location: index.php?stran=internacionala");
                                    }
                                }
                            }
                        }
                    }
                    mysqli_close($link);
                }
            }
        }
        
        
    #End Region

    #Regeon ** Cookie **
        //Preverjanje piskotka
        function PreveriPiskotek(){
            if (isset($_COOKIE['piskot'])){
                $vrednost = $_COOKIE['piskot'];
                $selektor = substr($vrednost, 0, 40);
                $validatorP = substr($vrednost, 40);
                $link=open_database_connection();
                $casZdaj = date("Y-m-d H:i:s");
                $sql = "SELECT UPORABNIK, ID_RANG FROM Blinker_IP WHERE SELEKTOR='$selektor' AND VALIDATOR='$validatorP' AND POTECE>'$casZdaj'";
                $rezultat = mysqli_query($link,$sql);
                $st_vrstic=mysqli_num_rows($rezultat);
        
                if ($st_vrstic == 1){
                    $vrstica=mysqli_fetch_row($rezultat);
                    $_SESSION['uporabnik'] = $vrstica[0];
                    $_SESSION['rang'] = $vrstica[1];
                } 
            }
        }

        //Funkcija za ustvarjanje selektorja
        function nakljucnaKoda($length){
            $token = "";
            $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
            $codeAlphabet.= "0123456789";
            $max = strlen($codeAlphabet);
            for ($i=0; $i < $length; $i++) {
                $token .= $codeAlphabet[random_int(0, $max-1)];
            }
            return $token;
        }
    #End Regeon

    #Regeon ** Uporabnik **
        //Funkcija za registracijo
        function registracija(){
            if(isset($_POST['gumb'])){
                $mail = strip_tags($_POST['email']);
                $username = strip_tags(trim($_POST['usrname']));
                $geslo = $_POST['passwd'];
                $geslo2 = $_POST['passwd2'];
                $kodiranoGeslo = password_hash($geslo, PASSWORD_DEFAULT);
                
                if (empty($username) || empty($mail) || empty($geslo) || empty($geslo2)){
                    echo '<script type="text/javascript"> alert("You left empty fields!") </script>';
                }else{
                    if($_POST["passwd"] != $_POST["passwd2"]){
                        echo '<script type="text/javascript"> alert("Passwords do not match!") </script>';
                    }else{
                        if(strlen($geslo) < 8 || strlen($geslo2) < 8){
                            echo '<script type="text/javascript"> alert("Password too short (8 characters minimum)!") </script>';
                        }else{
                            $link = open_database_connection();
                            $sql = "SELECT UPORABNIK, MAIL FROM Blinker_IP WHERE UPORABNIK=? OR MAIL=?";
                            $params = array($username, $mail);
                            $rezultat = mysqli_execute_query($link, $sql, $params);
                            
                            if ($rezultat === false){
                                echo '<script type="text/javascript"> alert("An error occurred while processing your request.") </script>';
                            }else{
                                $st_vrstic = mysqli_num_rows($rezultat);
                                
                                if ($st_vrstic == 1){
                                    echo '<script type="text/javascript"> alert("User already exists!") </script>';
                                }else{
                                    if ($st_vrstic == 0){
                                        echo '<script type="text/javascript"> alert("Welcome!") </script>';
                                        $selektor = nakljucnaKoda(40);
                                        $validator = password_hash(nakljucnaKoda(40), PASSWORD_DEFAULT);
                                        $sql = "INSERT INTO Blinker_IP (UPORABNIK, MAIL, GESLO, ID_RANG, VALIDATOR, SELEKTOR)
                                                VALUES (?, ?, ?, 3, ?, ?)";
                                        $params = array($username, $mail, $kodiranoGeslo, $validator, $selektor);
                                        mysqli_execute_query($link, $sql, $params);
                                        close_database_connection($link);
                                        header("Location: index.php?stran=prijava");
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        //Funkcija za spremembo gesla
        function sprememba_gesla() {
            if(isset($_POST['gumb'])){
                $starogeslo = $_POST['starogeslo'];
                $novogeslo0 = $_POST['novogeslo0'];
                $novogeslo1 = $_POST['novogeslo1'];
                $username = $_SESSION['uporabnik'];
                
                $link = open_database_connection();
                $sql = "SELECT UPORABNIK, GESLO FROM Blinker_IP WHERE UPORABNIK=?";
                $params = array($username);
                $rezultat = mysqli_execute_query($link, $sql, $params);
                
                if($rezultat === false){
                    echo '<script type="text/javascript"> alert("An error occurred while processing your request.") </script>';
                } else {
                    if(empty($starogeslo) || empty($novogeslo0) || empty($novogeslo1)){
                        echo '<script type="text/javascript"> alert("You left empty fields!") </script>';
                    } else {
                        if($novogeslo0 != $novogeslo1){
                            echo '<script type="text/javascript"> alert("Passwords do not match!") </script>';
                        } else {
                            if(strlen($novogeslo0) < 8 || strlen($novogeslo1) < 8){
                                echo '<script type="text/javascript"> alert("Too short password (8 characters minimum)!") </script>';
                            } else {
                                $vrstica = mysqli_fetch_row($rezultat);
                                
                                if (!password_verify($starogeslo, $vrstica[1])){
                                    echo '<script type="text/javascript"> alert("Wrong user password!") </script>';
                                } else {
                                    echo '<script type="text/javascript"> alert("Password changed successfully!") </script>';
                                    $knovogeslo = password_hash($novogeslo0, PASSWORD_DEFAULT);
                                    $sql = "UPDATE Blinker_IP SET GESLO=? WHERE UPORABNIK=?";
                                    $params = array($knovogeslo, $username);
                                    mysqli_execute_query($link, $sql, $params);
                                    odjava();
                                }
                            }
                        }
                    }
                }
            }
        }
        

        //Funkcija za spremembo uporabniskega imena
        function sprememba_usrn() {
            if(isset($_POST['gumb'])){
                $NovoUpIme = strip_tags(trim($_POST['upime']));
                $geslo = $_POST['geslo'];
                $username = $_SESSION['uporabnik'];
                
                $link = open_database_connection();
                $sql = "SELECT UPORABNIK, GESLO FROM Blinker_IP WHERE UPORABNIK=?";
                $params = array($username);
                $rezultat = mysqli_execute_query($link, $sql, $params);
                
                if($rezultat === false){
                    echo '<script type="text/javascript"> alert("An error occurred while processing your request.") </script>';
                } else {
                    $vrstica = mysqli_fetch_row($rezultat);
                    
                    if(empty($NovoUpIme) || empty($geslo)){
                        echo '<script type="text/javascript"> alert("You left empty fields!") </script>';
                    } else {
                        if($username == $NovoUpIme){
                            echo '<script type="text/javascript"> alert("User already exists!") </script>';
                        } else {
                            if(!password_verify($geslo, $vrstica[1])){
                                echo '<script type="text/javascript"> alert("Wrong password!") </script>';
                            } else {
                                $sql = "UPDATE Blinker_IP SET UPORABNIK=? WHERE UPORABNIK=?";
                                $params = array($NovoUpIme, $username);
                                mysqli_execute_query($link, $sql, $params);
                                odjava();
                            }
                        }
                    }
                }
            }
        }

        function odjava(){      
            session_destroy();
            setcookie("piskot", "zbrisano :)", time()-1);
            header('Location: index.php?stran=internacionala');
        }

        //Pridobivanje informacij o uporabniku
        function userInformation(){
            $link = open_database_connection();
            $username = $_SESSION['uporabnik'];
            $userID = uporabnikId($username);
            $sql = "SELECT UPORABNIK, extract(day from DATUM) as d, extract(month from DATUM) as m, extract(year from DATUM) as y, ID_RANG, GROUP_ID FROM Blinker_IP WHERE ID = $userID";
            $rezultat = mysqli_query($link, $sql);
            $userInfo = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
            return $userInfo;

        }
    #End Regeon

    #Region ** Torrenti **
        function dodajTorrent() {
            if (isset($_POST['gumb'])) {
                $link = open_database_connection();
                $naslov = strip_tags(trim($_POST['naslov']));
                $kategorija = $_POST['kategorija'];
                $file = $_FILES['file'];
                $filename = $file['name'];
                $extention = pathinfo($filename, PATHINFO_EXTENSION);
                $allowedExtensions = array(
                    // *** AUDIO ***
                    "mp3", "wav", "aiff",      
                    // *** VIDEO ***
                    "avi", "mkv", "mp4",        
                    "m4v", "mov",               
                    "mpg", "mpeg", "mpg2",      
                    // *** GAMES ***
                    "exe",                      
                    "iso",                      
                    // *** SOFTWARE ***
                    "exe",                      
                    "dmg",                      
                    "deb", "rpm",              
                    // *** APPS ***
                    "exe",                      
                    "dmg",                     
                    "deb", "rpm",              
                    "deb", "rpm",               
                    "ipa",                     
                    "apk",                     
                    // *** FILES ***
                    "doc", "docx",              
                    "xls", "xlsx",             
                    "ppt", "pptx",              
                    "pdf",                     
                    "zip", "7z", "rar"          
                );
        
                if (empty($naslov) || $kategorija == 0) {
                    echo '<script type="text/javascript"> alert("You left empty fields!") </script>';
                } else {
                    if (!in_array($extention, $allowedExtensions)) {
                        echo '<script type="text/javascript"> alert("Error uploading file: ' . $extention . '") </script>';
                    } else {
                        $sql = "SELECT NASLOV FROM Torrenti WHERE NASLOV = ?";
                        $params = [$naslov];
                        $rezultat = mysqli_execute_query($link, $sql, $params);
                        $st_vrstic = mysqli_num_rows($rezultat);
        
                        if ($st_vrstic == 1) {
                            echo '<script type="text/javascript"> alert("Torrent already exists!") </script>';
                        } else {
                            $torrentDatoteka = str_replace(' ', '_', $filename);
                            $file123 = "files/" . $torrentDatoteka;
                            $fileFullPath = "/var/www/html/zverina/files/" . $torrentDatoteka;
        
                            if (file_exists($fileFullPath)) {
                                echo '<script type="text/javascript"> alert("File already exists!") </script>';
                            } else {
                                if ($st_vrstic == 0) {
                                    $username = $_SESSION['uporabnik'];
        
                                    $sql = "SELECT ID FROM Blinker_IP WHERE UPORABNIK=?";
                                    $params = [$username];
                                    $rezultat = mysqli_execute_query($link, $sql, $params);
                                    $vrstica = mysqli_fetch_row($rezultat);
                                    $id_uporabnika = $vrstica[0];
        
                                    $path = "/var/www/html/zverina/files";
                                    $tmp_name = $file['tmp_name'];
                                    $name = basename($torrentDatoteka);
        
                                    move_uploaded_file($tmp_name, "$path/$name");
        
                                    $sqlID = "SELECT MAX(ID) FROM Torrenti";
                                    $rezultat = mysqli_query($link, $sqlID);
                                    $vrstica = mysqli_fetch_row($rezultat);
                                    $IdNovegaTorrenta = $vrstica[0] + 1;
        
                                    $sql = "INSERT INTO Torrenti (NASLOV, ID_UP, DATOTEKA, KATEGORIJA) VALUES (?, ?, ?, ?)";
                                    $params = [$naslov, $id_uporabnika, $file123, $kategorija];
                                    mysqli_execute_query($link, $sql, $params);
        
                                    $location = (vrniRang() == 1) ? "index.php?stran=admin_main" : "index.php?stran=mod_main";
                                    header("Location: " . $location);
                                }
                            }
                        }
                    }
                }
            }
        }

        function urediTorrent($id) {
            if (isset($_POST['gumb'])) {
                $link = open_database_connection();
                $statusTorrenta = $_POST['stat'];
                $novaKategorija = $_POST['sel'];
                $naslov = strip_tags(trim(str_replace("'", "\'", $_POST['naslov'])));
                $file = $_FILES['file'];
                $filename = $file['name'];
                $extention = pathinfo($filename, PATHINFO_EXTENSION);
                $allowedExtensions = array(
                    // *** AUDIO ***
                    "mp3", "wav", "aiff",       
                    // *** VIDEO ***
                    "avi", "mkv", "mp4",        
                    "m4v", "mov",               
                    "mpg", "mpeg", "mpg2",      
                    // *** GAMES ***
                    "exe",                      
                    "iso",                      
                    // *** SOFTWARE ***
                    "exe",                      
                    "dmg",                      
                    "deb", "rpm",              
                    // *** APPS ***
                    "exe",                    
                    "dmg",    
                    "deb", "rpm",
                    "deb", "rpm",              
                    "ipa",                      
                    "apk",                      
                    // *** FILES ***
                    "doc", "docx",              
                    "xls", "xlsx",              
                    "ppt", "pptx",             
                    "pdf",                     
                    "zip", "7z", "rar"          
                );
        
                if(empty($naslov)){
                    echo '<script type="text/javascript"> alert("You left empty fields!") </script>';
                }else{
                    if (vrniRang() == 1) {
                        if (isset($_FILES['file']['name']) && !empty($_FILES['file']['name']) && $_FILES['file']['error'] === UPLOAD_ERR_OK && in_array($extention, $allowedExtensions)) {
                            $new_file_tmp = $_FILES['file']['tmp_name'];
                            $upload_dir = '/var/www/html/zverina/files/';
        
                            $oldFile = '/var/www/html/zverina/' . getFile($id);
                            unlink($oldFile);
        
                            $original_filename = $_FILES['file']['name'];
        
                            $new_file_name = str_replace(' ', '_', $original_filename);
        
                            move_uploaded_file($new_file_tmp, $upload_dir . $new_file_name);
        
                            $newFIlePath = "files/" . $new_file_name;
                            $sql = "UPDATE Torrenti SET NASLOV=?, KATEGORIJA=?, STAT=?, DATOTEKA=? WHERE ID=?";
                            $params = [$naslov, $novaKategorija, $statusTorrenta, $newFIlePath, $id];
                        }else{
                            $sql = "UPDATE Torrenti SET NASLOV=?, KATEGORIJA=?, STAT=? WHERE ID=?";
                            $params = [$naslov, $novaKategorija, $statusTorrenta, $id];
                        }
                    }else{
                        if (vrniRang() == 2) {
                            $sql = "UPDATE Torrenti SET NASLOV=?, KATEGORIJA=? WHERE ID=?";
                            $params = [$naslov, $novaKategorija, $id];
                        }
                    }
                    mysqli_execute_query($link, $sql, $params);
                    $location = (vrniRang() == 1) ? "index.php?stran=admin_main&kategorija=seznamTorrentov&listOpt=2" : "index.php?stran=mod_main&kategorija=seznamTorrentov&listOpt=2";
                    header("Location: " . $location);
                }
            }
        }               

        //Izpis seznama uporabnikov
        function seznamUporabnikov(){
            $link = open_database_connection();
            $sql = "SELECT ID, UPORABNIK FROM Blinker_IP ORDER BY UPORABNIK ASC";
            $rezultat = mysqli_query($link, $sql);
            echo  "<option value='' disabled>Izberi uporabnike</option>";
            if($rezultat > 0){
                foreach ($rezultat as $uporabnik){
                    $userId = $uporabnik['ID'];
                    $username = $uporabnik['UPORABNIK'];
        
                    $isSelected = ($userId == uporabnikId($_SESSION['uporabnik'])) ? 'selected' : '';
                    echo "<option value='$userId' $isSelected>$username</option>";
                }
            }
        }

        function preveripraviceDatoteke($ID, $idUporabnika){
            $link = open_database_connection();
            $sql = "SELECT COUNT(*) as count FROM Pravice WHERE ID_DATOTEKE='$ID' AND ID_UP='$idUporabnika'";
            $rezultat = mysqli_query($link, $sql);
            
            if($rezultat){
                $row = mysqli_fetch_assoc($rezultat);
                return $row['count'];
            }else{
                return 0;
            }
        }

        function izbraniUporabniki(){
            if(isset($_POST['gumb'])){
                $link = open_database_connection();
                $izbraniUporabniki = $_POST['uporabniki'];
                $id=maxID();
        
                foreach($izbraniUporabniki as $posamezniUp){
                    $posamezniUp = (int)$posamezniUp;
                    if(preveripraviceDatoteke($id, $posamezniUp)==0){
                        $sql = "INSERT INTO Pravice (ID_DATOTEKE, ID_UP) VALUES ('$id', '$posamezniUp')";
                        mysqli_query($link, $sql);
                    }
                }
            }
        }  

        //Funkcija za posodabljanje pravic uporabnikom
        function izbraniUporabnikiposodobi(){
            if(isset($_POST['gumb'])){
                $link = open_database_connection();
                $izbraniUporabniki = $_POST['uporabniki'];
                $id = $_GET['id'];
        
                $sqlRemove = "DELETE FROM Pravice WHERE ID_DATOTEKE='$id' AND ID_UP NOT IN (" . implode(',', $izbraniUporabniki) . ")";
                mysqli_query($link, $sqlRemove);
        
                foreach ($izbraniUporabniki as $posamezniUp){
                    $posamezniUp = (int)$posamezniUp;
                    
                    if(preveripraviceDatoteke($id, $posamezniUp) == 0){
                        $sql = "INSERT INTO Pravice (ID_DATOTEKE, ID_UP) VALUES ('$id', '$posamezniUp')";
                        mysqli_query($link, $sql);
                    }
                }
            }
        }
        
        //Preverjanje statusa za datoteke
        function preveriStatus(){
            $link=open_database_connection();
            $id=$_GET['id'];
            $sql="SELECT STAT FROM Torrenti WHERE ID='$id'";
            $rezultat=mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            if($vrstica[0]==1){
                echo "<option value='1' selected>Public</option>";
                echo "<option value='0'>Privat</option>";
            }else{
                echo "<option value='0' selected>Privat</option>";
                echo "<option value='1'>Public</option>";
            }
            close_database_connection($link);
        }

        function izbraneKategorijeIskanja(){
            if(isset($_POST['gumb'])){
                $selectedValues = array();
                if(isset($_POST['checkbox'])){
                    $selectedValues = $_POST['checkbox'];
                }
                return $selectedValues;
            }
        }

        function vsiTorrenti(){
            $link = open_database_connection();
            $keyword = strip_tags(trim($_POST['keyword']));

            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);

            if(isset($_POST['gumb']) && !empty($keyword)){
                if(isset($_SESSION['uporabnik'])){
                    $groupID=usersGroupID(uporabnikId($_SESSION['uporabnik']));
                    $userID=uporabnikId($_SESSION['uporabnik']);
                    $sql = "SELECT DISTINCT t.ID, t.NASLOV, 
                            extract(day from t.DATUM) as d, extract(month from t.DATUM) as m, extract(year from t.DATUM) as y, 
                            t.STAT, t.ID_UP, t.KATEGORIJA, t.DATOTEKA 
                            FROM Torrenti t
                            JOIN Pravice p ON t.ID = p.ID_DATOTEKE
                            JOIN Blinker_IP b ON b.ID = p.ID_UP
                            LEFT JOIN GroupAccess ga ON ga.FILE_ID = t.ID
                            WHERE (p.ID_UP = ? AND t.NASLOV LIKE ?) OR (ga.GROUP_ID = ? AND t.NASLOV LIKE ?) OR (p.ID_UP = ? AND t.NASLOV LIKE ?) OR (t.STAT = 1 AND t.NASLOV LIKE ?)
                            ORDER BY t.DATUM DESC";
                    $params = [uporabnikId($_SESSION['uporabnik']), "%$keyword%", $groupID, "%$keyword%", $userID, "%$keyword%", "%$keyword%"];
                }else{
                    $sql = "SELECT DISTINCT t.ID, t.NASLOV, 
                            extract(day from t.DATUM) as d, extract(month from t.DATUM) as m, extract(year from t.DATUM) as y, 
                            t.STAT, t.ID_UP, t.KATEGORIJA, t.DATOTEKA 
                            FROM Torrenti t
                            JOIN Pravice p ON t.ID = p.ID_DATOTEKE
                            JOIN Blinker_IP b ON b.ID = p.ID_UP
                            LEFT JOIN GroupAccess ga ON ga.FILE_ID = t.ID
                            WHERE t.NASLOV LIKE ? AND t.STAT=1
                            ORDER BY t.DATUM DESC";
                            $params = ["%$keyword%"];
                }
            }else{
                if(isset($_SESSION['uporabnik'])){
                    $IDUP = uporabnikId($_SESSION['uporabnik']);
                    $userGroupID = usersGroupID($IDUP);
                    $sql = "SELECT DISTINCT t.ID, t.NASLOV, 
                            extract(day from t.DATUM) as d, extract(month from t.DATUM) as m, extract(year from t.DATUM) as y, 
                            t.STAT, t.ID_UP, t.KATEGORIJA, t.DATOTEKA 
                            FROM Torrenti t
                            JOIN Pravice p ON t.ID = p.ID_DATOTEKE
                            JOIN Blinker_IP b ON b.ID = p.ID_UP
                            LEFT JOIN GroupAccess ga ON ga.FILE_ID = t.ID
                            WHERE (p.ID_UP = ? OR t.STAT = 1)
                            OR (ga.GROUP_ID = ? AND t.NASLOV LIKE ?)
                            ORDER BY t.DATUM DESC";
                    $params = [$IDUP, $userGroupID, "%$keyword%"];
                }else{
                    $sql = "SELECT DISTINCT t.ID, t.NASLOV, 
                            extract(day from t.DATUM) as d, extract(month from t.DATUM) as m, extract(year from t.DATUM) as y, 
                            t.STAT, t.ID_UP, t.KATEGORIJA, t.DATOTEKA 
                            FROM Torrenti t
                            JOIN Pravice p ON t.ID = p.ID_DATOTEKE
                            JOIN Blinker_IP b ON b.ID = p.ID_UP
                            WHERE t.STAT = 1 AND t.NASLOV LIKE ?
                            ORDER BY t.DATUM DESC";
                    $params = ["%$keyword%"];
                }
            }
            $torrent = mysqli_execute_query($link, $sql, $params);
            return $torrent;
        }        
        
        //Pridibivanje ID-ja od novice
        function Torrent($id){
            $link=open_database_connection();
            $sql = "SELECT ID, NASLOV, extract(day from DATUM) as d, extract(month from DATUM) as m, extract(year from DATUM) as y, DATOTEKA, ID_UP, KATEGORIJA FROM Torrenti WHERE ID=$id";
            $rezultat = mysqli_query($link,$sql);
            $torrent = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
            return $torrent;
        }

        //Seznam moznih kategorij za datoteko
        function selectKatUrejanjeTorrenta($numKategorija){
            $kategorije = [
                "Audio" => [
                    101 => "Music",
                    102 => "Audio Books",
                    103 => "MP3",
                    104 => "Other Audio",
                ],
                "Video" => [
                    201 => "Xvid",
                    202 => "DVDRip",
                    203 => "WEBRip",
                    204 => "BlueRay 720p",
                    205 => "BlueRay 1080p",
                    206 => "4K",
                    207 => "Other Video",
                ],
                "Games" => [
                    301 => "PC Games",
                    302 => "Xbox Games",
                    303 => "PS Games",
                    304 => "Wii Games",
                ],
                "Software" => [
                    401 => "Windows Software",
                    402 => "Mac Software",
                    403 => "Linux Software",
                    404 => "Other Software",
                ],
                "Apps" => [
                    501 => "Windows Apps",
                    502 => "Mac Apps",
                    503 => "UNIX Apps",
                    504 => "Linux Apps",
                    505 => "iOS iPhone/iPad Apps",
                    506 => "Android Apps",
                    507 => "Other Apps",
                ],
                "Files" => [
                    601 => "Word Files",
                    602 => "Excel Files",
                    603 => "PowerPoint Files",
                    604 => "PDF Files",
                    605 => "Zip Files",
                ],
            ];
        
            $kategorijaTorrenta = $numKategorija;
            foreach ($kategorije as $group => $kategorijeGrupa){
                echo "<optgroup label='$group'>";
                foreach ($kategorijeGrupa as $kategorijaID => $kategorijaNaziv){
                    $selected = ($kategorijaID == $kategorijaTorrenta) ? 'selected' : '';
                    echo "<option value='$kategorijaID' $selected>$kategorijaNaziv</option>";
                }
                echo "</optgroup>";
            }
        }

    #End Region

    #Region ** Admin **
        //Izpis vseh datotek za administrativni del
        function vsiTorrentiAdministracija(){
            $link=open_database_connection();
            $keyword=strip_tags(trim($_POST['keyword']));
            $IDUP=uporabnikId($_SESSION['uporabnik']);
            //Admin
            if($_SESSION['rang']==1){
                if(isset($_POST['gumb1'])){
                    $sql = "SELECT ID, NASLOV, DATUM, DATOTEKA FROM Torrenti WHERE NASLOV LIKE '%$keyword%' ORDER BY ID DESC";
                }else{
                    $sql = "SELECT ID, NASLOV, DATUM, DATOTEKA FROM Torrenti ORDER BY ID DESC";
                }
            }else{
                if($_SESSION['rang']==2){
                    if(isset($_POST['gumb1'])){
                        $sql = "SELECT ID, NASLOV, DATUM, DATOTEKA FROM Torrenti WHERE (NASLOV LIKE '%$keyword%' and ID_UP=$IDUP) or STAT=1 ORDER BY ID DESC";
                    }else{
                        $sql = "SELECT DISTINCT t.ID, t.NASLOV, 
                        extract(day from t.DATUM) as d, extract(month from t.DATUM) as m, extract(year from t.DATUM) as y, 
                        t.STAT, t.ID_UP, t.KATEGORIJA, t.DATOTEKA 
                        FROM Torrenti t
                        JOIN Pravice p ON t.ID = p.ID_DATOTEKE
                        JOIN Blinker_IP b ON b.ID = p.ID_UP
                        WHERE p.ID_UP='$IDUP' OR t.STAT=1 or t.ID_UP='$IDUP'
                        ORDER BY t.ID DESC";
                    }
                }
            }

            $rezultat = mysqli_query($link, $sql);
            $torrent = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
            return $torrent;
        }

        //Seznam naloženih datotek za ternutno prijavljenega uporabnika
        function listOwnFiles(){
            $link=open_database_connection();
            $keyword=strip_tags(trim($_POST['keyword']));
            $IDUP=uporabnikId($_SESSION['uporabnik']);

            if(isset($_POST['gumb1'])){
                $sql = "SELECT ID, NASLOV, DATUM, DATOTEKA FROM Torrenti WHERE NASLOV LIKE '%$keyword%' AND ID_UP='$IDUP' ORDER BY ID DESC";
            }else{
                $sql = "SELECT ID, NASLOV, DATUM, DATOTEKA FROM Torrenti where ID_UP='$IDUP' ORDER BY ID DESC";
            }

            $rezultat = mysqli_query($link, $sql);
            $torrent = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
            return $torrent;
        }

        //Funkcija za izpis uporabnikov v bazi
        function rank(){
            $link=open_database_connection();
            $uporabnik=$_SESSION['uporabnik'];
            $sql="SELECT UPORABNIK, ID_RANG, ID, extract(day from DATUM) as d, extract(month from DATUM) as m, extract(year from DATUM) as l, extract(day from ZADNJAPRIJAVA) as dz, extract(month from ZADNJAPRIJAVA) as mz, extract(year from ZADNJAPRIJAVA) as lz, extract(hour from ZADNJAPRIJAVA) as hh, extract(minute from ZADNJAPRIJAVA) as mm, extract(second from ZADNJAPRIJAVA) as ss, MAIL FROM Blinker_IP WHERE ID != 1 ORDER BY ID_RANG ASC";
            
            $rezultat=mysqli_query($link, $sql);
            $vse=mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
        
            echo "<form method='post' id='nivoji' name='nivoji'>";
            
            $rangi=array();
            $i=0;
            foreach ($vse as $up){
                $x=$up['ID'];
                $rangi[$i]=$x;
                $i++;
                    if($up['UPORABNIK']==$_SESSION['uporabnik']){
                        echo "<b style='color: rgb(190, 190, 190);'>".$i.". ".$up['UPORABNIK']."</b><br>";
                        echo "<p style='display: inline; color: rgb(190, 190, 190)'>Rank: </p><select name='".$x."' style='border-radius: 5px; margin-bottom: 1.6%' disabled class='selecti'>";
                    }else{
                        echo "<hr style='border-top: 4px solid yellow; width: 25%'>";
                        echo "<p style='color: rgb(190, 190, 190); margin-bottom: 0px; margin-top: 8px; display: inline;'>".$i.". ".$up['UPORABNIK']."<br><i style='font-size:90%'>Member since: <u>".$up['d'].". ".$up['m'].". ".$up['l']."</u><br>Last login: <u>".$up['dz'].". ".$up['mz'].". ".$up['lz']." ".$up['hh'].":".$up['mm'].":".$up['ss']."</u><br>Email: <u>".$up['MAIL']."</u></i></p><br>";
                        echo "<p style='display: inline; color: rgb(190, 190, 190)'>Rank: </p><select name='".$x."' style='border-radius: 5px; display: inline; margin-bottom: 1.6%;' class='selecti'>";
                    }
                if($up['ID_RANG']==1){
                    echo "<option value=1 selected>Admin</option>";
                    echo "<option value=2>Moderator</option>";
                    echo "<option value=3>User</option>";
                    echo "<option value=4>BLOCKED</option>";
                }else{
                    if($up['ID_RANG']==2){
                        echo "<option value=2 selected>Moderator</option>";
                        echo "<option value=1>Admin</option>";
                        echo "<option value=3>User</option>";
                        echo "<option value=4>BLOCKED</option>";
                    }else{
                        if($up['ID_RANG']==3){
                            echo "<option value=3 selected>User</option>";
                            echo "<option value=2>Moderator</option>";
                            echo "<option value=1>Admin</option>";
                            echo "<option value=4>BLOCKED</option>";
                        }else{
                            if($up['ID_RANG']==4){
                                echo "<option value=4 selected>BLOCKED</option>";
                                echo "<option value=1>Admin</option>";
                                echo "<option value=2>Moderator</option>";
                                echo "<option value=3>User</option>";
                            }
                        }
                    }
                }
                echo "</select><br>";
            }
            echo "<br><button type='submit' class='btn btn-primary btn12'  name='gumb' id='gumb'>Save</button>";
            echo "</form>"; 
            if(isset($_POST['gumb'])){
                foreach($rangi as $rang){
                    $upID=uporabnikId($uporabnik);
                    $noviRang=$_POST[$rang];
                    $sql="UPDATE Blinker_IP SET ID_RANG='$noviRang' WHERE ID='$rang' AND ID!='$upID'";
                    mysqli_query($link, $sql);
                    echo $upID." ";
                }
                header("Location: index.php?stran=admin_main");
            }
        }
    #End Region

    #Region ** Ostale funkcije **
        //Funkcija za posodabljanje stolpca ZADNJAPRIJAVA
        function last_action(){
            $username=$_SESSION['uporabnik'];
            $zadnjaprijava=date("Y-m-d H:i:s");
            $link=open_database_connection();
            $sql="UPDATE Blinker_IP SET ZADNJAPRIJAVA='$zadnjaprijava' WHERE UPORABNIK='$username'";
            mysqli_query($link, $sql);
        }

        //Funkcija za vrnitev ranga it tabele Blinker_IP
        function vrniRang(){
            $link=open_database_connection();
            $username = $_SESSION['uporabnik'];
            $sql = "SELECT ID_RANG FROM Blinker_IP WHERE UPORABNIK='$username'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            $id_rang=$vrstica[0];
            return $id_rang;
        }

        //Pridobivanje imena uporabnika z vrednostjo ID
        function uporabnikIme($IdUporabnika){
            $link=open_database_connection();
            $sql="SELECT UPORABNIK FROM Blinker_IP WHERE ID='$IdUporabnika'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        //Prdobivanje ID-ja uporabnika z njegovim imenom
        function uporabnikId($ImeUporabnika){
            $link=open_database_connection();
            $sql="SELECT ID FROM Blinker_IP WHERE UPORABNIK='$ImeUporabnika'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function uporabnikSelektor($ImeUporabnika){
            $link=open_database_connection();
            $sql="SELECT SELEKTOR FROM Blinker_IP WHERE UPORABNIK='$ImeUporabnika'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function imeKategorije($kategorijaNum){
            $link=open_database_connection();
            $sql="SELECT KATEGORIJA FROM Kategorije WHERE ID='$kategorijaNum'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function numKategorije($imeKategorije){
            $link=open_database_connection();
            $sql="SELECT ID FROM Kategorije WHERE KATEGORIJA='$imeKategorije'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function maxID(){
            $link=open_database_connection();
            $sql="SELECT MAX(ID) FROM Torrenti";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function uporabnikiPravice($id){
            $link = open_database_connection();
            $sql = "SELECT ID_DATOTEKE, UP_ID FROM Pravice WHERE ID_DATOTE='$id'";
            $rezultat = mysqli_query($link, $sql);
        
            $pravice = array();
            if ($rezultat) {
                while ($vrstica = mysqli_fetch_assoc($rezultat)) {
                    $pravice[] = $vrstica;
                }
            }
            return $pravice;
        }
    #End Regeon

    #Region ** Grupe **

    function GroupSelection(){
        $link = open_database_connection();
        $userID = uporabnikId($_SESSION['uporabnik']);
        $uporabnik = $_SESSION['uporabnik'];
        $sql = "SELECT ID, UPORABNIK, ID_RANG, GROUP_ID FROM Blinker_IP WHERE ID != $userID AND ID != 1 ORDER BY ID_RANG ASC";
    
        $rezultat = mysqli_query($link, $sql);
        $vse = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
    
        echo "<form method='post' id='nivoji' name='nivoji'>";
        $rangi = array();
        foreach ($vse as $up) {
            $upID = $up['ID'];
            $rangi[] = $upID;
    
            $uporabnik = $up['UPORABNIK'];
            $groupID = $up['GROUP_ID'];
            if ($groupID != NULL) {
                $groupSQL = "SELECT GROUP_IME FROM Groups WHERE ID = $groupID";
                $groupResult = mysqli_query($link, $groupSQL);
                $groupRow = mysqli_fetch_assoc($groupResult);
                $groupName = $groupRow['GROUP_IME'];
            }
    
            if ($up['UPORABNIK'] == $_SESSION['uporabnik']) {
                echo "<b style='color: rgb(190, 190, 190);'>".$uporabnik."</b><br>";
                echo "<p style='display: inline; color: rgb(190, 190, 190)'>Group: </p><select name='$upID' style='border-radius: 5px; margin-bottom: 1.6%' disabled class='selecti'>";
            } else {
                echo "<hr style='border-top: 4px solid yellow; width: 25%'>";
                echo "<p style='color: rgb(190, 190, 190); margin-bottom: 0px; margin-top: 8px; display: inline;'>".$uporabnik."</p><br>";
                echo "<p style='display: inline; color: rgb(190, 190, 190)'>Group: </p><select name='$upID' style='border-radius: 5px; display: inline; margin-bottom: 1.6%;' class='selecti'>";
            }
    
            // Add an option for no group if GROUP_ID is NULL
            if ($groupID === NULL) {
                echo "<option value='' selected>No Group</option>";
            }
    
            $groupSQL = "SELECT ID, GROUP_IME FROM Groups";
            $groupResult = mysqli_query($link, $groupSQL);
    
            while ($groupRow = mysqli_fetch_assoc($groupResult)) {
                $groupID = $groupRow['ID'];
                $groupName = $groupRow['GROUP_IME'];
    
                if ($groupID == $up['GROUP_ID']) {
                    echo "<option value='$groupID' selected>$groupName</option>";
                } else {
                    echo "<option value='$groupID'>$groupName</option>";
                }
            }
            echo "</select><br>";
        }
    
        echo "<br><button type='submit' class='btn btn-primary btn12'  name='gumb' id='gumb'>Save</button>";
        echo "</form>"; 
    
        if (isset($_POST['gumb'])) {
            foreach ($rangi as $rang) {
                $noviRang = $_POST[$rang];
                // Check if the selected group is not empty
                if (!empty($noviRang)) {
                    $sql = "UPDATE Blinker_IP SET GROUP_ID='$noviRang' WHERE ID='$rang'";
                    mysqli_query($link, $sql);
                    //echo $noviRang." ";
                }
            }
            header("Location: index.php?stran=admin_main");
        }
    }

        //Izpis grup
        function seznamGrup(){
            $link = open_database_connection();
            $userId = uporabnikId($_SESSION['uporabnik']);
        
            $sql = "SELECT ID, GROUP_IME FROM Groups ORDER BY GROUP_IME ASC";
            $rezultat = mysqli_query($link, $sql);
            
            $userGroupSQL = "SELECT GROUP_ID FROM Blinker_IP WHERE ID = $userId";
            $userGroupResult = mysqli_query($link, $userGroupSQL);
            $userGroup = mysqli_fetch_assoc($userGroupResult);
            $userGroupId = $userGroup['GROUP_ID'];
        
            echo "<option value='' disabled>Izberi uporabnike</option>";
        
            if($rezultat && mysqli_num_rows($rezultat) > 0){
                foreach ($rezultat as $group){
                    $groupId = $group['ID'];
                    $groupName = $group['GROUP_IME'];
                    
                    $selected = ($groupId == $userGroupId) ? 'selected' : '';
                    
                    echo "<option value='$groupId' $selected>$groupName</option>";
                }
            }else{
                echo "<option value=''>No groups found</option>";
            }
        }

        //Posodabljanje pravic za grupe
        function izbraneGrupePosodobi(){
            if (isset($_POST['gumb'])) {
                $link = open_database_connection();
                $izbraneGrupe = $_POST['grupe'];
                $id = $_GET['id'];
                
                if(empty($izbraneGrupe)){
                    $sqlDeleteAllGroups = "DELETE FROM GroupAccess WHERE FILE_ID='$id'";
                    mysqli_query($link, $sqlDeleteAllGroups);
                }else{
                    $existingGroups = array();
                    $sqlExisting = "SELECT GROUP_ID FROM GroupAccess WHERE FILE_ID='$id'";
                    $resultExisting = mysqli_query($link, $sqlExisting);
                    if($resultExisting && mysqli_num_rows($resultExisting) > 0){
                        while ($row = mysqli_fetch_assoc($resultExisting)){
                            $existingGroups[] = $row['GROUP_ID'];
                        }
                    }
            
                    foreach($existingGroups as $existingGroup){
                        if(!in_array($existingGroup, $izbraneGrupe)){
                            $sqlRemove = "DELETE FROM GroupAccess WHERE FILE_ID='$id' AND GROUP_ID='$existingGroup'";
                            mysqli_query($link, $sqlRemove);
                        }
                    }
            
                    foreach($izbraneGrupe as $groupID){
                        $groupID = (int)$groupID;
                        if(!in_array($groupID, $existingGroups)){
                            $sql = "INSERT INTO GroupAccess (FILE_ID, GROUP_ID) VALUES ('$id', '$groupID')";
                            mysqli_query($link, $sql);
                        }
                    }
                }
            }
        }

        function usersGroupID($userID){
            $link=open_database_connection();
            $sql="SELECT GROUP_ID FROM Blinker_IP WHERE ID='$userID'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function groupName($groupID){
            $link=open_database_connection();
            $sql="SELECT GROUP_IME FROM Groups WHERE ID='$groupID'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function rangStatus($rangID){
            $link=open_database_connection();
            $sql="SELECT ID_RANG FROM Nivoji WHERE ID='$rangID'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function filesUploaded($userID){
            $link=open_database_connection();
            $sql="SELECT COUNT(*) FROM Torrenti WHERE ID_UP='$userID'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }

        function getFile($ID){
            $link=open_database_connection();
            $sql="SELECT DATOTEKA FROM Torrenti WHERE ID='$ID'";
            $rezultat = mysqli_query($link, $sql);
            $vrstica=mysqli_fetch_row($rezultat);
            return $vrstica[0];
        }
    #End Regeon

    function seznamOsebja(){
        $link = open_database_connection();
        $sql = "SELECT ID, UPORABNIK FROM Blinker_IP WHERE ID_RANG=1 or ID_RANG=2 ORDER BY UPORABNIK ASC";
        $rezultat = mysqli_query($link, $sql);
        echo  "<option value='' disabled>Izberi uporabnike</option>";
        if($rezultat > 0){
            foreach($rezultat as $uporabnik){
                $userId = $uporabnik['ID'];
                $username = $uporabnik['UPORABNIK'];
    
                $isSelected = ($userId == uporabnikId($_SESSION['uporabnik'])) ? 'selected' : '';
                echo "<option value='$userId' $isSelected>$username</option>";
            }
        }
    }
    
    function maxIdTiket(){
        $link=open_database_connection();
        $sql="SELECT MAX(ID) FROM Tiketi";
        $rezultat = mysqli_query($link, $sql);
        $vrstica=mysqli_fetch_row($rezultat);
        return $vrstica[0];
    }

    //Funkcija za dodajanje novega tiketa
    function dodajTicket() {
        if(isset($_POST['gumb'])){
            $link = open_database_connection();
            $naslov = strip_tags(trim($_POST['naslov']));
            $opis = strip_tags(trim($_POST['opis']));
            $handler = (int)$_POST['seznamOsebja'];
            $idUporabnika = (int)uporabnikId($_SESSION['uporabnik']);
            $uporabnik = $_SESSION['uporabnik'];
            
            $celotnaVsebina = $uporabnik . ": " . $opis;
    
            $potvsebine = "vsebineTiketov/ticket_" . (maxIdTiket() + 1) . ".txt";
            file_put_contents($potvsebine, $celotnaVsebina);
    
            if(!empty($naslov) && !empty($opis)){
                $sql = "INSERT INTO Tiketi (TIKET_IME, TIKET_VSEBINA, ID_UP, HANDLER) VALUES (?, ?, ?, ?)";
                $params = array($naslov, $potvsebine, $idUporabnika, $handler);
                mysqli_execute_query($link, $sql, $params);
                header("Location: index.php?stran=profil");
            } else {
                echo '<script type="text/javascript"> alert("You left empty fields in the form!") </script>';
            }
        }
    }
    

    //Seznam tiketov na katere je uporabnik dodan kot HANDLER
    function mojiTiketi(){
        $link = open_database_connection();
        $IDUP = uporabnikId($_SESSION['uporabnik']);
        $vrstaIskanjaTiketov = $_POST['tiketiPoKategorijah'];
        if(isset($_POST['gumb1'])){
            $sql = "SELECT ID, TIKET_IME, TIKET_VSEBINA, STAT, DODANO FROM Tiketi WHERE HANDLER=$IDUP AND STAT='$vrstaIskanjaTiketov' ORDER BY ID DESC";
        }else{
            $sql = "SELECT ID, TIKET_IME, TIKET_VSEBINA, STAT, DODANO FROM Tiketi WHERE HANDLER=$IDUP AND STAT=1 ORDER BY ID DESC";
        }
        
        $rezultat = mysqli_query($link, $sql);
        $tiketi = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
        return $tiketi;
    }

    //Seznam tiketov katere je uporabnik poslal
    function userTickets(){
        $link = open_database_connection();
        $IDUP = uporabnikId($_SESSION['uporabnik']);
        $sql = "SELECT ID, TIKET_IME, TIKET_VSEBINA, STAT, DODANO FROM Tiketi WHERE ID_UP=$IDUP AND STAT=1 ORDER BY ID DESC";
        
        $rezultat = mysqli_query($link, $sql);
        $tiketi = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
        return $tiketi;
    }

    //Informacije o posameznem tiketu
    function Tiket($id){
        $link=open_database_connection();
        $sql = "SELECT ID, TIKET_IME, extract(day from DODANO) as d, extract(month from DODANO) as m, extract(year from DODANO) as y, ID_UP, TIKET_VSEBINA FROM Tiketi WHERE ID=$id";
        $rezultat = mysqli_query($link,$sql);
        $tiket = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
        return $tiket;
    }

    function vsebinaTiketa($id){
        $link=open_database_connection();
        $sql="SELECT TIKET_VSEBINA FROM Tiketi WHERE ID='$id'";
        $rezultat=mysqli_query($link, $sql);
        $vrstica=mysqli_fetch_row($rezultat);
        return $vrstica[0];
    }

    function updateTiket() {
        if(isset($_POST['gumb'])){
            $link = open_database_connection();
            $idTiketa = $_GET['id'];
            $naslov = strip_tags(trim($_POST['naslov']));
            $vsebina = strip_tags(trim($_POST['vsebina']));
            $statusTiketa = $_POST['statusTiketa'];
            $uporabnik = $_SESSION['uporabnik'];
    
            if(!empty($naslov) && !empty($vsebina)){
                $pot = vsebinaTiketa($idTiketa);
                $originalVsebina = file_get_contents($pot);
                $celotnaVsebina = $originalVsebina . "\n" . $uporabnik . ": " . $vsebina;
                file_put_contents($pot, $celotnaVsebina);
    
                $sql = "UPDATE Tiketi SET TIKET_IME=?, STAT=? WHERE ID=?";
                $params = array($naslov, $statusTiketa, $idTiketa);
                
                if($_SESSION['rang'] == 1 && $_GET['stran']=="admin_main"){
                    header("Location: index.php?stran=admin_main&kategorija=mojitiketi");
                }elseif($_SESSION['rang'] == 2 && $_GET['stran']=="mod_main") {
                    header("Location: index.php?stran=mod_main&kategorija=mojitiketi");
                }elseif($_GET['stran']=="profil") {
                    header("Location: index.php?stran=profil&kategorija=mytickets");
                }
                mysqli_execute_query($link, $sql, $params);
            }
        }
    }
    

    function Threads(){
        $link = open_database_connection();
        $sql = "SELECT ID, THREAD_NASLOV, UP_ID FROM Threads ORDER BY ID DESC";
        
        $rezultat = mysqli_query($link, $sql);
        $tiketi = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
        return $tiketi;
    }

    function dodajThread(){
        if(isset($_POST['gumb'])) {
            $link = open_database_connection();
            $naslov = strip_tags(trim($_POST['naslov']));
            $opis = strip_tags(trim($_POST['opis']));
            $idUporabnika = uporabnikId($_SESSION['uporabnik']);
    
            if(!empty($naslov) && !empty($opis)){
                $potvsebine = "vsebineThreads/thread_" . (maxIdThread() + 1) . ".txt";
                file_put_contents($potvsebine, $opis);
                $sql = "INSERT INTO Threads (THREAD_NASLOV, THREAD_VSEBINA, UP_ID) VALUES (?, ?, ?)";
                $params = [$naslov, $potvsebine, $idUporabnika];
                mysqli_execute_query($link, $sql, $params);
                header("Location: index.php?stran=forum");
            }else{
                echo '<script type="text/javascript"> alert("You left empty fields in the form!") </script>';
            }
        }
    }
    

    function maxIdThread(){
        $link=open_database_connection();
        $sql="SELECT MAX(ID) FROM Threads";
        $rezultat=mysqli_query($link, $sql);
        $vrstica=mysqli_fetch_row($rezultat);
        return $vrstica[0];
    }

    function threadInfo($id){
        $link=open_database_connection();
        $sql="SELECT ID, THREAD_NASLOV, THREAD_VSEBINA, UP_ID, extract(day from DATUM_OBJAVE) as d, extract(month from DATUM_OBJAVE) as m, extract(year from DATUM_OBJAVE) as y, extract(hour from DATUM_OBJAVE) as hr, extract(minute from DATUM_OBJAVE) as min, extract(second from DATUM_OBJAVE) as s FROM Threads WHERE ID='$id'";
        $rezultat = mysqli_query($link,$sql);
        $threadInfo = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
        return $threadInfo;
    }

    function submitKomentar() {
        if(isset($_POST['gumb'])){
            $link = open_database_connection();
            $idUporabnika=uporabnikId($_SESSION['uporabnik']);
            $odgovorNaThread=$_GET['id'];
            $komentar=strip_tags(trim($_POST['comment']));
            if(!empty($komentar) && strlen($komentar)<255){
                $sql="INSERT INTO Answers (ANSWER_TEXT, UP_ID, ANSWER_TO) VALUES (?, ?, ?)";
                $params = [$komentar, $idUporabnika, $odgovorNaThread];
                mysqli_execute_query($link, $sql, $params);
                header("Location: index.php?stran=forum");
            }
        }
    }
    

    function vsiKomentarji($id){
        $link = open_database_connection();
        $sql="SELECT ID, ANSWER_TEXT, UP_ID, extract(day from DATUM_KOMENTARJA) as d, extract(month from DATUM_KOMENTARJA) as m, extract(year from DATUM_KOMENTARJA) as y, extract(hour from DATUM_KOMENTARJA) as hr, extract(minute from DATUM_KOMENTARJA) as min, extract(second from DATUM_KOMENTARJA) as s FROM Answers WHERE ANSWER_TO='$id'";
        $rezultat = mysqli_query($link,$sql);
        $torrent = mysqli_fetch_all($rezultat, MYSQLI_ASSOC);
        return $torrent;
    }

    function posodobiThread($id) {
        if(isset($_POST['gumbPosodobi'])){
            $link = open_database_connection();
            $newThread=strip_tags(trim($_POST['vsebina']));
            $threadTitle=strip_tags(trim($_POST['naslov']));
    
            if(!empty($newThread) && !empty($threadTitle)){
                $potvsebine=potObstojeceVsebine($id);
                file_put_contents($potvsebine, $newThread);
                $sql="UPDATE Threads SET THREAD_NASLOV=? WHERE ID=?";
                $params = [$threadTitle, $id];
                mysqli_execute_query($link, $sql, $params);
                header("Location: index.php?stran=forum");
            }
        }
    }
    

    function potObstojeceVsebine($id){
        $link=open_database_connection();
        $sql="SELECT THREAD_VSEBINA FROM Threads WHERE ID=$id";
        $rezultat=mysqli_query($link, $sql);
        $vrstica=mysqli_fetch_row($rezultat);
        return $vrstica[0];
    }
?>