<!-- resources/views/auth/register.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Register - Erlass Ekskul</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-container {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: left;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .btn {
            padding: 10px 20px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .btn:hover {
            background-color: #1976D2;
        }

        .error {
            color: red;
            margin-top: 5px;
        }

        .link {
            display: block;
            text-align: center;
            margin-top: 10px;
            color: #555;
        }

        .link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2 class="text-center mb-4">Register Instruktur</h2>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <!-- Nama Lengkap -->
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input 
                    type="text" 
                    id="nama_lengkap" 
                    name="nama_lengkap" 
                    value="{{ old('nama_lengkap') }}" 
                    required 
                    autofocus
                >
                @error('nama_lengkap')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Email -->
            <div class="form-group">
                <label for="email">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    value="{{ old('email') }}" 
                    required
                >
                @error('email')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Password -->
            <div class="form-group">
                <label for="password">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    required 
                    autocomplete="new-password"
                >
                @error('password')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Confirm Password -->
            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    required 
                    autocomplete="new-password"
                >
                @error('password_confirmation')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Tanggal Lahir -->
            <div class="form-group">
                <label for="tanggal_lahir">Tanggal Lahir</label>
                <input 
                    type="date" 
                    id="tanggal_lahir" 
                    name="tanggal_lahir" 
                    value="{{ old('tanggal_lahir') }}" 
                    required
                >
                @error('tanggal_lahir')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- No. Telephone -->
            <div class="form-group">
                <label for="no_telephone">No. Telephone</label>
                <input 
                    type="text" 
                    id="no_telephone" 
                    name="no_telephone" 
                    value="{{ old('no_telephone') }}" 
                    required
                >
                @error('no_telephone')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Agama -->
            <div class="form-group">
                <label for="agama">Agama</label>
                <input 
                    type="text" 
                    id="agama" 
                    name="agama" 
                    value="{{ old('agama') }}" 
                    required
                >
                @error('agama')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Pendidikan Terakhir -->
            <div class="form-group">
                <label for="pend_terakhir">Pendidikan Terakhir</label>
                <input 
                    type="text" 
                    id="pend_terakhir" 
                    name="pend_terakhir" 
                    value="{{ old('pend_terakhir') }}" 
                    required
                >
                @error('pend_terakhir')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kompetensi 1 -->
            <div class="form-group">
                <label for="kompetensi_1">Kompetensi 1</label>
                <input 
                    type="text" 
                    id="kompetensi_1" 
                    name="kompetensi_1" 
                    value="{{ old('kompetensi_1') }}" 
                    required
                >
                @error('kompetensi_1')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Kompetensi 2 (Optional) -->
            <div class="form-group">
                <label for="kompetensi_2">Kompetensi 2 (Opsional)</label>
                <input 
                    type="text" 
                    id="kompetensi_2" 
                    name="kompetensi_2" 
                    value="{{ old('kompetensi_2') }}"
                >
                @error('kompetensi_2')
                    <div class="error">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="form-group">
                <button type="submit" class="btn">Register</button>
            </div>

            <!-- Already Registered Link -->
            <div class="link">
                <a href="{{ route('login') }}">Already registered?</a>
            </div>
        </form>
    </div>
</body>
</html>