$adminFiles = @("adm_dashboard.php", "manage_students.php", "add_scholarship.php", "view_scholarships.php", "applied_student.php", "approve.php", "year_wise_summary.php", "manage_feedback.php", "settings.php", "list_names.php")

foreach ($file in $adminFiles) {
    $path = "c:\xampp\htdocs\SMS\admin\$file"
    if (Test-Path $path) {
        $content = Get-Content $path -Raw
        if ($content -notmatch "session\.php") {
            $content = $content -replace "(?s)^(<\?php\s*)", "`$1require_once 'session.php';`n"
            Set-Content -Path $path -Value $content -NoNewline
            Write-Host "Updated $file"
        }
    }
}

$studentFiles = @("stu_dashboard.php", "stu_profile.php", "stu_feedback.php", "stu_changePassword.php", "apply_scholarship_latest.php")
foreach ($file in $studentFiles) {
    $path = "c:\xampp\htdocs\SMS\$file"
    if (Test-Path $path) {
        $content = Get-Content $path -Raw
        if ($content -match "session_start\(\);") {
            $content = $content -replace "session_start\(\);", "require_once 'session.php';"
            Set-Content -Path $path -Value $content -NoNewline
            Write-Host "Updated $file"
        } elseif ($content -notmatch "session\.php") {
            $content = $content -replace "(?s)^(<\?php\s*)", "`$1require_once 'session.php';`n"
            Set-Content -Path $path -Value $content -NoNewline
            Write-Host "Updated $file"
        }
    }
}
