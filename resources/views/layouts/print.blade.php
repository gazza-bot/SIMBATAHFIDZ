<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Cetak - SIMBA TAHFIDZ' }}</title>
    
    {{-- Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito+Sans:wght@400;500;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
    
    <style>
        @page {
            margin: 1.5cm;
            size: A4;
        }
        
        @media print {
            .no-print { display: none !important; }
            body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
        
        * { box-sizing: border-box; }
        body { font-family: 'Nunito Sans', sans-serif; font-size: 12px; line-height: 1.5; color: #1E2922; }
        .font-arabic { font-family: 'Amiri', serif; line-height: 2.2; text-align: right; direction: rtl; }
        
        .page-header { text-align: center; margin-bottom: 24px; padding-bottom: 16px; border-bottom: 2px solid #4A7C59; }
        .page-header .logo { width: 60px; height: 60px; margin: 0 auto 12px; background: #4A7C59; border-radius: 12px; display: flex; align-items: center; justify-content: center; }
        .page-header h1 { font-size: 20px; font-weight: 700; color: #1E2922; margin: 0; }
        .page-header p { font-size: 12px; color: #64746B; margin: 4px 0 0; }
        
        .page-footer { margin-top: 24px; padding-top: 16px; border-top: 1px solid #E2E7E4; text-align: center; font-size: 10px; color: #94A39B; }
        
        .card { background: white; border: 1px solid #E2E7E4; border-radius: 12px; padding: 16px; margin-bottom: 16px; }
        .card-title { font-size: 14px; font-weight: 600; color: #1E2922; margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid #E2E7E4; }
        
        .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; }
        
        .info-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #F1F4F2; }
        .info-row:last-child { border-bottom: none; }
        .info-label { color: #64746B; }
        .info-value { font-weight: 500; color: #1E2922; text-align: right; }
        
        .badge { display: inline-flex; align-items: center; padding: 4px 10px; border-radius: 9999px; font-size: 11px; font-weight: 500; }
        .badge-success { background: #DCFCE7; color: #15803D; border: 1px solid #86EFAC; }
        .badge-info { background: #EFF6FF; color: #1D4ED8; border: 1px solid #93C5FD; }
        .badge-warning { background: #FEF9C3; color: #854D0E; border: 1px solid #FDE047; }
        .badge-error { background: #FEE2E2; color: #991B1B; border: 1px solid #FCA5A5; }
        
        .table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .table th, .table td { padding: 8px; text-align: left; border-bottom: 1px solid #E2E7E4; }
        .table th { background: #F8FAF9; font-weight: 600; color: #334139; }
        .table tr:last-child td { border-bottom: none; }
        
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .mt-4 { margin-top: 16px; }
        .mb-4 { margin-bottom: 16px; }
        .flex { display: flex; }
        .justify-between { justify-content: space-between; }
        .items-center { align-items: center; }
        .gap-4 { gap: 16px; }
    </style>
</head>
<body>
    @yield('content')
</body>
</html>