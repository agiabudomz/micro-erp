<?php
require_once 'config/db.php';
session_start();

// 1. PROTEÇÃO DE ROTA: Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$msg = "";
$status = ""; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone = $_POST['phone'];
    $pass = $_POST['password'];
    
    if (isset($_POST['register_active']) && $_POST['register_active'] == "1") {
        $name = $_POST['name'];
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        
        // Verifica se telefone já existe
        $check = $conn->prepare("SELECT id FROM users WHERE phone = ?");
        $check->bind_param("s", $phone);
        $check->execute();
        
        if ($check->get_result()->num_rows > 0) {
            $msg = "Este telefone já está cadastrado.";
            $status = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $phone, $hash);
            if ($stmt->execute()) {
                $msg = "Conta criada com sucesso! Pode entrar.";
                $status = "success";
            } else {
                $msg = "Erro ao processar cadastro.";
                $status = "error";
            }
        }
    } else {
        $stmt = $conn->prepare("SELECT * FROM users WHERE phone = ?");
        $stmt->bind_param("s", $phone);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            header("Location: dashboard.php");
            exit;
        } else { 
            $msg = "Credenciais inválidas. Tente novamente."; 
            $status = "error";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acessar - MicroERP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .transition-all { transition: all 0.3s ease; }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .animate-shake { animation: shake 0.4s ease-in-out; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md">
        
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center mb-6">
                <img src="./logo.png" class="w-[160px] h-auto object-contain" alt="MicroERP Logo">
            </div>
            <h1 id="view_title" class="text-2xl font-bold text-slate-900 tracking-tight">Bem-vindo de volta</h1>
            <p id="view_subtitle" class="text-slate-500 text-sm mt-1">Acesse sua conta para gerenciar vendas.</p>
        </div>

        <div class="bg-white rounded-[8px] shadow-2xl shadow-slate-200/60 p-8 border border-slate-100">
            
            <?php if($msg): ?>
                <div class="flex items-center gap-3 p-4 rounded-[8px] mb-6 text-sm border <?= $status == 'success' ? 'bg-green-50 border-green-100 text-green-600' : 'bg-red-50 border-red-100 text-red-600 animate-shake' ?>">
                    <ion-icon name="<?= $status == 'success' ? 'checkmark-circle' : 'alert-circle' ?>" class="text-xl flex-shrink-0"></ion-icon>
                    <span class="font-semibold"><?= $msg ?></span>
                </div>
            <?php endif; ?>

            <form method="POST" id="auth_form" class="space-y-5">
                
                <input type="hidden" name="register_active" id="register_active" value="0">

                <div id="field_name" class="hidden transition-all duration-300">
                    <label class="text-[10px] font-bold text-slate-400 ml-1 mb-2 block uppercase tracking-widest">Nome Completo</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="person-outline" class="text-xl"></ion-icon>
                        </div>
                        <input type="text" name="name" id="input_name"
                            class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-[8px] text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all placeholder-slate-400" 
                            placeholder="Seu nome">
                    </div>
                </div>

                <div class="group">
                    <label class="text-[10px] font-bold text-slate-400 ml-1 mb-2 block uppercase tracking-widest">Telefone / WhatsApp</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="phone-portrait-outline" class="text-xl"></ion-icon>
                        </div>
                        <input type="tel" name="phone" required 
                            class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-[8px] text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all placeholder-slate-400" 
                            placeholder="Ex: 840000000">
                    </div>
                </div>

                <div class="group">
                    <label class="text-[10px] font-bold text-slate-400 ml-1 mb-2 block uppercase tracking-widest">Sua Senha</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-slate-400">
                            <ion-icon name="lock-closed-outline" class="text-xl"></ion-icon>
                        </div>
                        <input type="password" name="password" required 
                            class="block w-full pl-11 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-[8px] text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:bg-white transition-all placeholder-slate-400" 
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" id="btn_submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-5 rounded-[8px] shadow-lg shadow-blue-100 flex items-center justify-center gap-2 transform active:scale-[0.98] transition-all mt-6">
                    <span id="text_submit" class="tracking-wide">Acessar Sistema</span>
                    <ion-icon name="log-in-outline" id="icon_submit" class="text-2xl"></ion-icon>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                <p class="text-slate-500 text-sm">
                    <span id="text_toggle_helper">Não possui uma conta?</span>
                    <button type="button" onclick="toggleAuthMode()" id="btn_toggle" 
                        class="text-blue-600 font-bold hover:text-blue-800 transition-colors ml-1">
                        Criar agora
                    </button>
                </p>
            </div>
        </div>

        <p class="mt-8 text-center text-slate-400 text-[10px] font-bold uppercase tracking-[0.2em]">
            &copy; 2025 MicroERP &bull; Mozambique
        </p>
    </div>

    <script>
        function toggleAuthMode() {
            const fieldName = document.getElementById('field_name');
            const inputName = document.getElementById('input_name');
            const registerActive = document.getElementById('register_active');
            
            const viewTitle = document.getElementById('view_title');
            const viewSubtitle = document.getElementById('view_subtitle');
            const textSubmit = document.getElementById('text_submit');
            const iconSubmit = document.getElementById('icon_submit');
            const btnToggle = document.getElementById('btn_toggle');
            const textHelper = document.getElementById('text_toggle_helper');

            if (registerActive.value === "0") {
                fieldName.classList.remove('hidden');
                inputName.required = true;
                registerActive.value = "1";
                
                viewTitle.innerText = "Crie sua conta";
                viewSubtitle.innerText = "Preencha os dados para começar";
                textSubmit.innerText = "Cadastrar agora";
                iconSubmit.setAttribute('name', 'person-add-outline');
                btnToggle.innerText = "Fazer login";
                textHelper.innerText = "Já tem uma conta?";
            } else {
                fieldName.classList.add('hidden');
                inputName.required = false;
                registerActive.value = "0";
                
                viewTitle.innerText = "Bem-vindo de volta";
                viewSubtitle.innerText = "Acesse sua conta para gerenciar vendas";
                textSubmit.innerText = "Acessar Sistema";
                iconSubmit.setAttribute('name', 'log-in-outline');
                btnToggle.innerText = "Criar agora";
                textHelper.innerText = "Não possui uma conta?";
            }
        }
    </script>
</body>
</html>