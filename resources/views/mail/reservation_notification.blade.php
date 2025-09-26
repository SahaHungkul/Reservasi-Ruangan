<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Reservasi Ruangan</title>
</head>

<body>
    <h2>Reservasi Baru</h2>
    <p>Room: {{ $reservation->room->name }}</p>
    <p>User: {{ $reservation->user->name }}</p>
    <p>Status: Pending</p>

    {{-- Tombol Approve --}}
    <form action="{{ route('reservations.approve', $reservation->id) }}" method="POST" style="display:inline;">
        @csrf
        <button type="submit" class="btn btn-success">Approve</button>
    </form>

    {{-- Tombol untuk menampilkan form Reject --}}
    <button onclick="document.getElementById('reject-form').style.display='block'" class="btn btn-danger">
        Reject
    </button>

    {{-- Form Reject (awal hidden) --}}
    <form id="reject-form" action="{{ route('reservations.reject', $reservation->id) }}" method="POST"
        style="display:none; margin-top:10px;">
        @csrf
        <label for="reason">Alasan Penolakan:</label>
        <input type="text" name="reason" id="reason" required class="form-control"
            placeholder="Tuliskan alasan..." />
        <br>
        <button type="submit" class="btn btn-danger">Kirim Penolakan</button>
        <button type="button" onclick="document.getElementById('reject-form').style.display='none'"
            class="btn btn-secondary">
            Batal
        </button>
    </form>
</body>

</html>
