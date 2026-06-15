</div>

<script>
function toggleTheme() {
    let body = document.getElementById("body");
    let btn = document.getElementById("themeBtn");

    if (body.classList.contains("dark")) {
        body.classList.remove("dark");
        body.classList.add("light");
        localStorage.setItem("theme", "light");

        btn.classList.remove("btn-dark");
        btn.classList.add("btn-light");
        btn.innerHTML = '<i class="fa fa-moon"></i>';

    } else {
        body.classList.remove("light");
        body.classList.add("dark");
        localStorage.setItem("theme", "dark");

        btn.classList.remove("btn-light");
        btn.classList.add("btn-dark");
        btn.innerHTML = '<i class="fa fa-sun"></i>';
    }
}

// LOAD SAVED THEME
window.onload = function () {
    let savedTheme = localStorage.getItem("theme");
    let body = document.getElementById("body");
    let btn = document.getElementById("themeBtn");

    if (savedTheme === "dark") {
        body.classList.add("dark");
        btn.classList.remove("btn-light");
        btn.classList.add("btn-dark");
        btn.innerHTML = '<i class="fa fa-sun"></i>';
    } else {
        body.classList.add("light");
        btn.classList.remove("btn-dark");
        btn.classList.add("btn-light");
        btn.innerHTML = '<i class="fa fa-moon"></i>';
    }
}
</script>

</body>
</html>