// Skills array
const skills = ['HTML5', 'CSS3', 'JavaScript (ES6+)', 'PHP', 'MySQL', 'Git & GitHub', 'Responsive Design', 'Flexbox/Grid'];
const skillsList = document.querySelector('.skills-list');
if (skillsList) {
    skills.forEach(skill => {
        const li = document.createElement('li');
        li.textContent = skill;
        skillsList.appendChild(li);
    });
}

// Toggle skills visibility
const toggleBtn = document.getElementById('toggleSkillsBtn');
function toggleSkillsVisibility() {
    if (skillsList.style.display === 'none') {
        skillsList.style.display = 'flex';
        toggleBtn.textContent = 'Hide Skills';
    } else {
        skillsList.style.display = 'none';
        toggleBtn.textContent = 'Show Skills';
    }
}
if (toggleBtn) toggleBtn.addEventListener('click', toggleSkillsVisibility);

// Projects data with dummy images & full descriptions
const projectsData = [
    {
        title: 'Task 1 – HTML/CSS Demo',
        shortDesc: 'A fully responsive page with forms, tables, multimedia, Flexbox/Grid, animations, and media queries.',
        fullDesc: 'This project demonstrates all core frontend skills required in Task 1. It includes semantic HTML5, advanced CSS3 (Flexbox, Grid, animations, media queries), and a fully responsive layout. The page also features an interactive form, embedded audio/video, and a data table.',
        tech: 'HTML5, CSS3, JavaScript (basic), XAMPP',
        image: 'https://placehold.co/600x400/0f172a/f59e0b?text=HTML5+CSS3+Demo'
    },
    {
        title: 'PHP Contact System',
        shortDesc: 'Backend form handling with MySQL. Stores messages in a database (local demo).',
        fullDesc: 'A complete contact form with PHP backend and MySQL database. Users can submit their name, email, and message; the data is securely stored in a database using MySQLi. The system also includes basic input sanitization and success/error messages.',
        tech: 'PHP, MySQL, Bootstrap, XAMPP',
        image: 'https://placehold.co/600x400/1e293b/f59e0b?text=PHP+MySQL+Backend'
    },
    {
        title: 'Portfolio Website',
        shortDesc: 'This very portfolio – built with modern HTML5, CSS3, and JavaScript, hosted on GitHub Pages.',
        fullDesc: 'A modern, fully responsive personal portfolio that showcases my skills, projects, and contact information. It includes dark mode, dynamic project cards, a skill list generator, and a modal dialog for project details. The site is hosted on GitHub Pages.',
        tech: 'HTML5, CSS3, JavaScript, Git, GitHub Pages',
        image: 'https://placehold.co/600x400/0f172a/f59e0b?text=Portfolio+Website'
    }
];

// Render projects dynamically
const projectsGrid = document.querySelector('.projects-grid');
if (projectsGrid) {
    projectsData.forEach((project, index) => {
        const card = document.createElement('div');
        card.className = 'project-card';
        card.innerHTML = `
            <img src="${project.image}" alt="${project.title}">
            <div class="project-card-content">
                <h3>${project.title}</h3>
                <p>${project.shortDesc}</p>
                <button class="project-btn" data-project-index="${index}">View Details →</button>
            </div>
        `;
        projectsGrid.appendChild(card);
    });
}

// Modal logic
const modal = document.getElementById('projectModal');
const modalTitle = document.getElementById('modalTitle');
const modalDescription = document.getElementById('modalDescription');
const modalTech = document.getElementById('modalTech');
const closeModal = document.querySelector('.close-modal');

function showProjectDetails(index) {
    const project = projectsData[index];
    modalTitle.textContent = project.title;
    modalDescription.textContent = project.fullDesc;
    modalTech.innerHTML = `<strong>Technologies:</strong> ${project.tech}`;
    modal.style.display = 'flex';
}

if (closeModal) {
    closeModal.onclick = () => { modal.style.display = 'none'; };
}
window.onclick = (event) => {
    if (event.target === modal) modal.style.display = 'none';
};

// Event delegation for project buttons
document.addEventListener('click', (e) => {
    if (e.target.classList.contains('project-btn')) {
        const index = e.target.getAttribute('data-project-index');
        if (index !== null) showProjectDetails(parseInt(index));
    }
});

// Dark mode toggle with localStorage
const darkModeBtn = document.getElementById('darkModeBtn');
if (darkModeBtn) {
    if (localStorage.getItem('theme') === 'dark') {
        document.body.classList.add('dark-mode');
        darkModeBtn.textContent = '☀️';
    }
    darkModeBtn.addEventListener('click', () => {
        document.body.classList.toggle('dark-mode');
        const isDark = document.body.classList.contains('dark-mode');
        localStorage.setItem('theme', isDark ? 'dark' : 'light');
        darkModeBtn.textContent = isDark ? '☀️' : '🌙';
    });
}
// Resume PDF generation using jsPDF
document.getElementById('downloadResumeBtn').addEventListener('click', function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Add content to PDF
    doc.setFontSize(22);
    doc.text("Madhu Sarvani", 20, 20);
    doc.setFontSize(12);
    doc.text("Full‑Stack Developer", 20, 30);
    doc.text("Email: madhusarvani626@gmail.com", 20, 40);
    doc.text("Location: India (Remote)", 20, 50);

    doc.setFontSize(16);
    doc.text("Summary", 20, 70);
    doc.setFontSize(12);
    doc.text("Passionate Full‑Stack Developer with expertise in HTML5, CSS3, JavaScript, PHP, MySQL,", 20, 80);
    doc.text("and Git. Skilled in building responsive web applications and solving complex problems.", 20, 88);

    doc.setFontSize(16);
    doc.text("Technical Skills", 20, 110);
    doc.setFontSize(12);
    doc.text("• HTML5, CSS3, JavaScript (ES6+)", 20, 120);
    doc.text("• PHP, MySQL, XAMPP", 20, 128);
    doc.text("• Git, GitHub, Responsive Design, Flexbox/Grid", 20, 136);
    doc.text("• REST APIs, Bootstrap (basic)", 20, 144);

    doc.setFontSize(16);
    doc.text("Experience", 20, 165);
    doc.setFontSize(12);
    doc.text("• Freelance Web Developer (2024 – Present)", 20, 175);
    doc.text("  - Built custom portfolio websites for clients.", 25, 183);
    doc.text("  - Integrated contact forms with PHP/MySQL.", 25, 191);
    doc.text("• Intern – Web Development (2023)", 20, 201);
    doc.text("  - Assisted in developing internal tools using HTML/CSS/JS.", 25, 209);

    doc.setFontSize(16);
    doc.text("Education", 20, 230);
    doc.setFontSize(12);
    doc.text("Bachelor of Computer Applications", 20, 240);
    doc.text("University of Technology, India – 2024", 20, 248);

    // Save the PDF
    doc.save("Madhu_Sarvani_Resume.pdf");
});