<!doctype html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    {{-- <link href="{{ asset('/build/assets/app-DhDuTjR3.css') }}" rel="stylesheet"> --}}

    <style>
body { margin: 0 }
@page {
    size: A4 landscape; /* Ukuran kertas A4 dalam orientasi landscape */
    margin: 0; /* Margin default, bisa disesuaikan */
}

@media print {
    body {
        width: 297mm;
        height: 210mm;
        margin: 0;
        padding: 0; /* Sesuaikan padding sesuai kebutuhan */
        box-sizing: border-box;
    }
}





        .container {
            max-width: 112rem;
            /* 7xl */
            margin-left: auto;
            margin-right: auto;
            padding-left: 1.5rem;
            /* sm:px-6 */
            padding-right: 1.5rem;
            /* sm:px-6 */
        }

        @media (min-width: 1024px) {
            .container {
                padding-left: 2rem;
                /* lg:px-8 */
                padding-right: 2rem;
                /* lg:px-8 */
            }
        }

        .card {
            background-color: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
            border-radius: 0.5rem;
            /* sm:rounded-lg */
        }

        .card-content {
            padding: 1.5rem;
            /* p-6 */
            color: #1a202c;
            /* text-gray-900 */
        }

        .table-container {
            position: relative;
        }

        .border-container {
            width: 100%;
            height: auto;
            border: 1rem solid black;
            /* 16px */
        }

        .inner-border {
            border: 0.125rem solid black;
            /* border-2 */
            margin: 2rem;
            /* m-8 */
            height: auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            /* grid-cols-2 */
            gap: 1rem;
            /* gap-4 */
        }

        .section {
            display: flex;
            flex-direction: column;
            /* flex-col */
            padding: 2rem;
            /* p-8 */
        }

        .section-content {
            flex: 1;
            /* flex-1 */
        }

        .title {
            font-size: 3rem;
            /* text-5xl */
            font-weight: 700;
            /* font-bold */
            margin-bottom: 4rem;
            /* mb-16 */
        }

        .badge {
            background-color: #1a202c;
            /* bg-gray-900 */
            color: white;
            font-weight: 700;
            /* font-bold */
            padding: 0.25rem;
            /* p-1 */
            padding-top: 0.5rem;
            /* pt-2 */
            border-radius: 0.125rem;
            /* rounded-sm */
        }

        .name {
            color: #3182ce;
            /* text-blue-400 */
            font-weight: 700;
            /* font-bold */
            font-size: 1.875rem;
            /* text-3xl */
        }

        .course {
            color: #3182ce;
            /* text-blue-400 */
            font-weight: 700;
            /* font-bold */
            font-size: 1.25rem;
            /* text-xl */
        }

        .date {
            margin-top: 4rem;
            /* mt-16 */
        }

        .signature {
            width: 6rem;
            /* w-24 */
        }

        .signaturey {
            width: 12rem;
            /* w-24 */
        }

        .signature img {
            width: 100%;
            margin-bottom: 0;
        }

        .signature-name {
            font-weight: 700;
            /* font-bold */
            margin-bottom: 0;
        }

        .signature-title {
            font-size: 0.75rem;
            /* text-xs */
            margin-bottom: 0;
        }

        .logo-container {
            display: flex;
            flex-direction: column;
            /* flex-col */
            align-items: flex-end;
            /* items-end */
        }

        .logo {
            width: 8rem;
            /* w-32 */
            margin-bottom: 4rem;
            /* mb-16 */
        }

        .logo img {
            width: 100%;
            display: inline-block;
        }

        .qrcode-container {
            display: flex;
            justify-content: flex-end;
            /* justify-end */
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <div class="card-content">
                <div class="table-container">
                    <div class="border-container">
                        <div class="inner-border">
                            <div id="printMe" class="grid">
                                <div class="section">
                                    <div class="section-content">
                                        <h1 class="title">Madrasah</h1>
                                        <span class="badge">RVZKOK6WDDMD</span>
                                        <p class="mt-3">Diberikan kepada</p>
                                        <h1 class="name">Fatkul Umar</h1>
                                        <p class="mt-3">Atas kelulusanya pada kelas</p>
                                        <h1 class="course">Desain Grafis Intermediate 1</h1>
                                        <p class="date">04 Mei 2024</p>
                                    </div>
                                    <div class="signature">
                                        <img src="/mpj.svg" alt="tanda tangan" />
                                    </div>
                                    <div class="signaturey">
                                        <span class="signature-name">Ahmad Tajudin Zahro'u</span>
                                        <span class="signature-title">Chief Executive Officer</span> <br>
                                        <span class="signature-title">Madrasah</span>
                                    </div>
                                </div>
                                <div class="section">
                                    <div class="logo-container">
                                        <div class="logo">
                                            <img src="/mpj.svg" alt="MPJ" />
                                        </div>
                                    </div>
                                    <div class="logo-container">
                                        <div class="logo">
                                            <img src="/mpj.svg" alt="MPJ" />
                                        </div>
                                    </div>
                                    <div class="qrcode-container">
                                        <vue-qrcode value="https://dicoding.com"></vue-qrcode>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
</body>

</html>
