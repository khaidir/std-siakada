# 20 — Prompt: Mahasiswa — AI Academic Advisor

```text
Buatkan fitur AI Academic Advisor (chat konsultasi akademik) untuk mahasiswa memakai Laravel AI SDK.

1. Halaman pages/mahasiswa/AiAdvisor.vue:
   - Antarmuka chat: daftar pesan (bubble user/AI), input + tombol kirim, indikator loading.
   - Riwayat percakapan tersimpan & dimuat saat halaman dibuka.
   - Disclaimer tetap tampil: "Jawaban AI bersifat saran, bukan keputusan akademik resmi."

2. Backend:
   - Tabel chat_messages (atau conversation + messages): id, student_id(fk), role(string user/assistant), content(text), timestamps.
   - app/DTO/ChatMessageData.php.
   - app/Repositories/Contracts/ChatRepository.php:
     - listForStudent(int $studentId, int $limit): Collection (SELECT id, role, content, created_at) urut ascending.
     - create(array $data): ChatMessage.
   - app/Services/AiAdvisorService.php:
     - buildContext(int $studentId): array — data mahasiswa (nim, nama, prodi, semester, ipk, total_sks), riwayat nilai ringkas, kurikulum prodi, peraturan SKS.
     - ask(int $studentId, string $message): string — panggil SDK AI (text generation) dengan system prompt berbahasa Indonesia + konteks; simpan user & assistant message; kembalikan jawaban.
   - app/Http/Controllers/Mahasiswa/AiAdvisorController.php (index, store) + AskRequest.php (validasi pesan tidak kosong & max length).
   - app/Policies/ChatPolicy.php (hanya miliknya).

3. Aturan:
   - Endpoint chat di-rate-limit (throttle).
   - Jawaban wajib menyertakan disclaimer.
   - Tidak ada SELECT *; konteks dibatasi hanya data relevan.

4. Pest test:
   - AiAdvisorTest: ask menyimpan pesan user+assistant; riwayat hanya milik mahasiswa; mahasiswa lain 403.
   - Mock SDK AI (tidak memanggil API eksternal sungguhan).
```
