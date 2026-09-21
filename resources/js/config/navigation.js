/**
 * Menu sidebar per role.
 *
 * `permission` menentukan visibilitas: item hanya dirender jika pengguna
 * memiliki permission tersebut (atau null untuk container sub-menu).
 * `href` berupa URL literal agar tidak error saat route fitur belum dibuat.
 */
const navigation = {
    'super-admin': [
        { label: 'Dashboard', href: '/admin/dashboard', icon: '🏠', permission: 'dashboard.view' },
        {
            label: 'Pengguna',
            icon: '👥',
            permission: null,
            children: [
                { label: 'Daftar Pengguna', href: '/admin/users', permission: 'users.view' },
                { label: 'Roles & Permissions', href: '/admin/roles', permission: 'roles.manage' },
            ],
        },
        {
            label: 'Master Data',
            icon: '🗂️',
            permission: null,
            children: [
                { label: 'Fakultas', href: '/admin/faculties', permission: 'master.view' },
                { label: 'Program Studi', href: '/admin/study-programs', permission: 'master.view' },
                { label: 'Mata Kuliah', href: '/admin/courses', permission: 'master.view' },
                { label: 'Ruangan', href: '/admin/classrooms', permission: 'master.view' },
                { label: 'Kelas & Jadwal', href: '/admin/course-offerings', permission: 'offering.manage' },
                { label: 'Periode Akademik', href: '/admin/periods', permission: 'period.manage' },
            ],
        },
        { label: 'KRS (Monitoring)', href: '/admin/krs-approval', icon: '📋', permission: 'krs.approve' },
        { label: 'Skripsi & KP', href: '/admin/skripsi-kp', icon: '🎓', permission: 'advisor.manage' },
        { label: 'Kehadiran Dosen', href: '/admin/kehadiran-dosen', icon: '🕐', permission: 'lecturer-attendance.view' },
        { label: 'Pengumuman', href: '/admin/announcements', icon: '📢', permission: 'announcement.manage' },
    ],
    kaprodi: [
        { label: 'Dashboard', href: '/kaprodi/dashboard', icon: '🏠', permission: 'dashboard.view' },
        { label: 'Mata Kuliah', href: '/kaprodi/courses', icon: '📚', permission: 'courses.view' },
        { label: 'Kelas & Jadwal', href: '/kaprodi/offerings', icon: '🗓️', permission: 'offerings.view' },
        { label: 'Monitoring Nilai', href: '/kaprodi/grades', icon: '📊', permission: 'grades.view' },
        { label: 'KRS (Monitoring)', href: '/kaprodi/krs', icon: '📋', permission: 'krs.approve' },
        { label: 'Skripsi & KP', href: '/kaprodi/skripsi-kp', icon: '🎓', permission: 'thesis.view' },
        { label: 'Kehadiran Dosen', href: '/kaprodi/kehadiran', icon: '🕐', permission: 'lecturer-attendance.view' },
        { label: 'Pengumuman', href: '/kaprodi/announcements', icon: '📢', permission: 'announcement.view' },
    ],
    dosen: [
        { label: 'Dashboard', href: '/dosen/dashboard', icon: '🏠', permission: 'dashboard.view' },
        { label: 'Kelas Saya', href: '/dosen/kelas', icon: '🏫', permission: 'offerings.view' },
        { label: 'Input Nilai', href: '/dosen/nilai', icon: '📝', permission: 'grades.manage' },
        { label: 'Presensi Kelas', href: '/dosen/presensi', icon: '✅', permission: 'attendance.manage' },
        { label: 'Materi Kuliah', href: '/dosen/materi', icon: '📁', permission: 'material.manage' },
        { label: 'Tugas & Penilaian', href: '/dosen/tugas', icon: '📝', permission: 'assignment.manage' },
        { label: 'Bimbingan PA (KRS)', href: '/dosen/bimbingan-pa', icon: '🧭', permission: 'krs.approve' },
        { label: 'Bimbingan Skripsi', href: '/dosen/bimbingan-skripsi', icon: '🎓', permission: 'thesis.view' },
        { label: 'Kehadiran Dosen', href: '/dosen/kehadiran', icon: '🕐', permission: 'lecturer-attendance.view' },
    ],
    mahasiswa: [
        { label: 'Dashboard', href: '/mahasiswa/dashboard', icon: '🏠', permission: 'dashboard.view' },
        { label: 'KRS (Pilih Mata Kuliah)', href: '/mahasiswa/krs', icon: '📋', permission: 'krs.manage' },
        { label: 'Jadwal Kuliah', href: '/mahasiswa/jadwal', icon: '🗓️', permission: 'offerings.view' },
        { label: 'Presensi', href: '/mahasiswa/presensi', icon: '✅', permission: 'attendance.view' },
        { label: 'Materi Kuliah', href: '/mahasiswa/materi', icon: '📁', permission: 'material.view' },
        { label: 'Tugas', href: '/mahasiswa/tugas', icon: '📝', permission: 'submission.manage' },
        { label: 'Nilai', href: '/mahasiswa/nilai', icon: '📊', permission: 'grades.view' },
        { label: 'KHS', href: '/mahasiswa/khs', icon: '📄', permission: 'grades.view' },
        { label: 'Transkrip', href: '/mahasiswa/transkrip', icon: '📜', permission: 'grades.view' },
        { label: 'AI Advisor', href: '/mahasiswa/ai-advisor', icon: '🤖', permission: 'ai-advisor.use' },
        { label: 'Skripsi', href: '/mahasiswa/skripsi', icon: '🎓', permission: 'thesis.view' },
        { label: 'Kerja Praktek', href: '/mahasiswa/kp', icon: '🏢', permission: 'internship.view' },
    ],
    pimpinan: [
        { label: 'Dashboard', href: '/pimpinan/dashboard', icon: '🏠', permission: 'dashboard.view' },
        {
            label: 'Laporan Akademik',
            icon: '📈',
            permission: 'report.view',
            children: [
                { label: 'KRS & KHS', href: '/pimpinan/laporan/krs-khs', permission: 'report.view' },
                { label: 'Transkrip & Presensi', href: '/pimpinan/laporan/transkrip-presensi', permission: 'report.view' },
                { label: 'Kinerja Dosen', href: '/pimpinan/laporan/kinerja-dosen', permission: 'report.view' },
            ],
        },
    ],
};

/**
 * Cari jejak breadcrumb (array label) untuk URL aktif pada menu role tertentu.
 *
 * @param {string} role
 * @param {string} url
 * @param {Array}  items
 * @param {Array}  trail
 * @returns {string[]|null}
 */
export function activeTrail(role, url, items = navigation[role] ?? [], trail = []) {
    for (const item of items) {
        if (item.href === url) return [...trail, item.label];

        if (item.children) {
            const found = activeTrail(role, url, item.children, [...trail, item.label]);
            if (found) return found;
        }
    }

    return null;
}

export default navigation;
