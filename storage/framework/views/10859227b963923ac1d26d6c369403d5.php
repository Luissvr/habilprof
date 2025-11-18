<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>HabilProf UCSC</title>

    
    <script src="https://cdn.tailwindcss.com"></script>

    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">

    
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>

    <style>
        .lista_alumno, .lista_profesores {
            max-height: 160px;
            overflow-y: auto;
            background: white;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            margin-top: 4px;
            padding: 6px;
            position: absolute;
            width: 100%;
            z-index: 999;
        }

        .lista_alumno div, .lista_profesores div {
            padding: 6px;
            cursor: pointer;
            border-radius: 6px;
        }

        .lista_alumno div:hover, .lista_profesores div:hover {
            background: #f3f4f6;
        }

        .ruts { position: relative; }
    </style>

    <style>
        .tab-hab {
            background: transparent;
            text-align: center;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            border-radius: 6px;
        }

        .tab-hab:hover {
            background: #fee2e2;
        }

        .activo-hab {
            background: #dc2626 !important;
            color: white !important;
            font-weight: 600;
        }
    </style>



    <style>
    .tab-boton {
        transition: all 0.2s ease-in-out;
        color: #FECACA; /* claro */
        background-color: transparent;
        font-weight: 500;
    }

    .tab-boton:hover {
        color: #FFFFFF;
        background-color: #B91C1C;
    }

    .tab-boton.activo {
        color: #FFFFFF;
        background-color: #991B1B;
        font-weight: 600;
        box-shadow: inset 0 -4px 0 0 #DC2626;
    }

    .tab-panel {
        display: none;
        animation: fadeIn 0.5s ease-out;
    }

    .tab-panel.activo {
        display: block;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

</style>


    <?php echo $__env->yieldPushContent('styles'); ?>

</head>
<body class="bg-gray-100 min-h-screen">

    
    <?php echo $__env->yieldContent('content'); ?>

    <?php echo $__env->yieldPushContent('scripts'); ?>

</body>
</html>
<?php /**PATH C:\Users\elias\OneDrive\Escritorio\tis 1\habilprof-Version_Actualizada\habilprof-Version_Actualizada\resources\views/layouts/app.blade.php ENDPATH**/ ?>