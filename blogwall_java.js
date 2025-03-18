/*Java för gilla knappen*/
// blogwall_java.js

document.addEventListener("DOMContentLoaded", function () {
    // Hantera gilla-knappar
    document.querySelectorAll(".like-btn").forEach(button => {
        button.addEventListener("click", function () {
            const postId = this.getAttribute("data-post-id");
            const likeCountSpan = this.querySelector(".like-count");

            fetch("like_post.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `post_id=${postId}`,
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        likeCountSpan.textContent = data.like_count;
                        this.classList.toggle("liked");
                    }
                })
                .catch((error) => {
                    console.error("Error:", error);
                });
        });
    });

    // Hantera "Visa mer"/"Visa mindre"-knappar
    document.querySelectorAll(".toggle-btn").forEach(button => {
        const content = button.closest(".post").querySelector(".content");
        const isOverflowing = content.scrollHeight > content.clientHeight;

        if (!isOverflowing) {
            button.style.display = "none";
        }

        button.addEventListener("click", function () {
            content.classList.toggle("short");
            this.textContent = content.classList.contains("short") ? "Show more" : "Show less";
        });
    });

    // Hantera modalen för att lägga till/redigera inlägg
    const modal = document.getElementById("postModal");
    const openModalBtn = document.getElementById("openModalBtn");
    const closeBtn = document.querySelector(".close-btn");

    if (openModalBtn) {
        openModalBtn.addEventListener("click", () => {
            modal.style.display = "flex";
        });
    }

    if (closeBtn) {
        closeBtn.addEventListener("click", () => {
            modal.style.display = "none";
        });
    }

    window.addEventListener("click", (e) => {
        if (e.target === modal) {
            modal.style.display = "none";
        }
    });

    // Hantera bildöverlägg
    const images = document.querySelectorAll(".post-img");
    const overlay = document.getElementById("overlay");

    images.forEach(img => {
        img.addEventListener("mouseenter", () => {
            overlay.style.visibility = "visible";
            overlay.style.opacity = "1";
        });

        img.addEventListener("mouseleave", () => {
            overlay.style.visibility = "hidden";
            overlay.style.opacity = "0";
        });
    });
});