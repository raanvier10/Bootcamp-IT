$files = @(
    "resources\views\layouts\admin.blade.php",
    "resources\views\admin\dashboard.blade.php",
    "resources\views\admin\reports.blade.php"
)

foreach ($f in $files) {
    $c = Get-Content $f -Raw
    $c = $c -replace 'bg-black', 'bg-primary'
    $c = $c -replace 'text-black', 'text-primary'
    $c = $c -replace 'ring-black', 'ring-primary'
    $c = $c -replace 'border-black', 'border-primary'
    $c = $c -replace 'hover:bg-gray-900', 'hover:bg-primary-deep'
    $c = $c -replace 'hover:text-black', 'hover:text-primary'
    
    $c = $c -replace 'text-green-600', 'text-primary'
    $c = $c -replace 'bg-green-50', 'bg-primary-soft'
    $c = $c -replace 'border-green-100/50', 'border-primary/20'
    $c = $c -replace 'border-green-200', 'border-primary/30'
    $c = $c -replace 'from-green-50', 'from-primary-soft'
    $c = $c -replace 'to-emerald-100', 'to-primary-soft'
    $c = $c -replace 'text-green-500', 'text-primary'
    $c = $c -replace 'text-green-800', 'text-primary-deep'

    Set-Content -Path $f -Value $c
}
