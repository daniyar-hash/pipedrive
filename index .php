  <?php
// Pipedrive API token
$api_token = 'e104f070e033f708f4add2f0eee9e8ff28d1c96a';


// Company domain
$company_domain = 'chastnoe-sandbox';

// --- Обработка отправки формы ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Собираем данные из формы
    $firstName = $_POST['firstName'] ?? '';
    $lastName  = $_POST['lastName'] ?? '';
    $phone     = $_POST['phone'] ?? '';
    $email     = $_POST['email'] ?? '';
    $jobType   = $_POST['jobType'] ?? '';
    $jobSource = $_POST['jobSource'] ?? '';
    $jobDesc   = $_POST['jobDescription'] ?? '';
    $address   = $_POST['address'] ?? '';
    $city      = $_POST['city'] ?? '';
    $state     = $_POST['state'] ?? '';
    $zip       = $_POST['zipCode'] ?? '';
    $area      = $_POST['area'] ?? '';
    $startDate = $_POST['startDate'] ?? '';
    $startTime = $_POST['startTime'] ?? '';
    $endTime   = $_POST['endTime'] ?? '';
    $technician      = $_POST['technician'] ?? '';



    $jobID = strtoupper(substr(md5($jobNumber . time()), 0, 6)); // например, "97CUTT"



    // Собираем данные для создания сделки
    $dealData = [
        'title'       => 'Job #',
        'value'       => 500,
        'currency'    => 'USD',
        'add_time'    => date('Y-m-d H:i:s'),
        '94fbfca7a14af54c3a9702f96e79aacb0c8afd0c' => $jobType,
        'fc61b926e2553a3eb84f46da808282c3d7e31956' => $jobSource,
        'b3fd6bc6fdd2f3ed66f5de15cdbe9e0f0b328d6b' => $jobDesc,
        'b1272a13685ed01a30e7ed73f83d495239ccfaea' =>$address,
        'e6d30971eae26de97a57bd0dec5cacebb2ee3c02'  =>$startDate,
        'c09dceb30ad62961d16a24cb8e476e2f8cfddc19' =>$startTime,
        '5b4cfbb70fc8cc4604cf19bb74e573e61d5b0ab2' =>$endTime,
        '305ec341a1ddc40cccbddd546136271ef6e12c24' =>$technician,
        '8f52803300691cd22ee79fcbb4dda0fea1896805' =>$jobID,
        '962f06eeb8981e7cf3f1c96a5789f2be50c46595' =>$area

        // Кастомные поля (пример)

    ];

    $url = "https://{$company_domain}.pipedrive.com/api/v1/deals?api_token={$api_token}";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($dealData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    $output = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $result = json_decode($output, true);


    // Вывод результата
if (!empty($result['data']['id'])) {
    $dealId   = $result['data']['id'];
    $dealLink = "https://{$company_domain}.pipedrive.com/deal/{$dealId}";

    // --- Второй запрос: обновляем сделку, добавляем ссылку в кастомное поле ---
    $updateUrl = "https://{$company_domain}.pipedrive.com/api/v1/deals/{$dealId}?api_token={$api_token}";
    
    $updateData = [
        'title' => 'Job # '.$dealId,
        '937057ac34e5ee7ea3b5eb4b0403da81fe5db1e7' => $dealLink,
        'e444bb0f4680c9254181b5f570a3ef6670058f58' => $dealId
       // 'e444bb0f4680c9254181b5f570a3ef6670058f58' =>$number // ← сюда твой ключ кастомного поля
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $updateUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($updateData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);

    $updateOutput = curl_exec($ch);
    curl_close($ch);

    $updateResult = json_decode($updateOutput, true);

    // --- Выводим на экран ---
    $successBlock =  "<div style='padding:15px;background:#d4edda;color:#155724;border:1px solid #c3e6cb;'>
            ✅ Job is created!  <a href='{$dealLink}' target='_blank'>View Deal!</a>
        
            
          </div>";
}






   else {
        $errorBlock =  "<div style='padding:15px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;'>
              ❌ Ошибка при создании сделки. Код: {$http_code}<br>
              Ответ: <pre>" . print_r($result, true) . "</pre></div>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client & Job Details Form</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
      <script src="https://cdn.jsdelivr.net/npm/@pipedrive/app-extensions-sdk@0/dist/index.umd.js"></script>

</head>
<body>
    <div class="container">
        <div class="header">
            <h1>New Create a job</h1>
        </div>
        
        <div class="form-content">

          <?php if ($successBlock): ?>
                <!-- Только успех -->
                <?= $successBlock ?>
            <?php else: ?>
                <!-- Ошибка (если была) -->
                <?= $errorBlock ?>




            <form id="details-form" method="post" action="#">
                <!-- Client Details Section -->
                <div class="form-section client-details">
                    <h2 class="section-title"><i class="fas fa-user-circle"></i> Client details</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="firstName"></label>
                            <input type="text" id="firstName" name="firstName" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="lastName"></label>
                            <input type="text" id="lastName" name="lastName" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone"></label>
                            <input type="tel" id="phone" name="phone" required value="(996)500000000">
                        </div>
                        
                        <div class="form-group">
                            <label for="email"><span class="optional"></span></label>
                            <input type="email" id="email" name="email" value="test@test">
                        </div>
                    </div>
                </div>
                
                <!-- Job Details Section -->
                <div class="form-section">
                    <h2 class="section-title"><i class="fas fa-briefcase"></i> Job type</h2>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <select id="jobType" name="jobType" required>
                                <option value="">Select job type</option>
                                <option value="recall job">Recall job</option>
                                <option value="repair">Repair</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="installation">Installation</option>
                                <option value="consultation">Consultation</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="jobSource">Job source</label>
                            <select id="jobSource" name="jobSource" required>
                                <option value="">Select job source</option>
                                <option value="website">Website</option>
                                <option value="referral">Referral</option>
                                <option value="social-media">Social Media</option>
                                <option value="advertisement">Advertisement</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="jobDescription"></label>
                            <textarea id="jobDescription" name="jobDescription" placeholder="Describe the job details..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Service Location Section -->
                <div class="form-section address">
                    <h2 class="section-title"><i class="fas fa-map-marker-alt"></i> Service location</h2>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label for="address" class="visually-hidden">Address</label>
                            <input type="text" id="address" name="address" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="city " class="visually-hidden">City</label>
                            <input type="text" id="city" name="city" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="state " class="visually-hidden">State</label>
                            <input type="text" id="state" name="state" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="zipCode" class="visually-hidden">Zip code</label>
                            <input type="text" id="zipCode" name="zipCode" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="area" class="visually-hidden">Area</label>
                            <input type="text" id="area" name="area" required>
                        </div>
                    </div>
                </div>
                
                <!-- Scheduled Section -->
                <div class="form-section">
                    <h2 class="section-title"><i class="fas fa-calendar-alt"></i> Scheduled</h2>
                    
                    <div class="form-row ">
                        <div class="form-group">
                            <label for="startDate">Start date</label>
                            <input type="date" id="startDate" name="startDate" required>
                        </div>
                    </div>
                    
                    <div class="form-row scheduled">
                        <div class="form-group">
                            <label for="startTime">Start time</label>
                            <input type="time" id="startTime" name="startTime" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="endTime">End time</label>
                            <input type="time" id="endTime" name="endTime" required>
                        </div>
                    </div>

                    <div class="form-row">
                            <div class="form-group">
                              <select id="technician" name="technician" required>
                                  <option value="">Select technician</option>
                                  <option value="website">Petr Ivanov</option>
                                  <option value="referral">Igor Sydorov</option>

                              </select>
                          </div>
                  </div>

                </div>
                   
                
                <div class="btn-container">
                    <button type="submit" class="btn-submit button">Create a job</button>
                       <button type="submit" class="btn_save-info button">Save info </i></button>

                </div>
            </form>


                <?php endif; ?>

        </div>
    </div>


   <script>
        
        // Set default date to today and time to current hour + 1
        const today = new Date();
        const tomorrow = new Date(today);
        tomorrow.setDate(tomorrow.getDate() + 1);
        
        document.getElementById('startDate').valueAsDate = tomorrow;
        document.getElementById('startTime').value = "09:00";
        document.getElementById('endTime').value = "17:00";

    </script>
    <script src="sdk.js"></script>
</body>
</htm