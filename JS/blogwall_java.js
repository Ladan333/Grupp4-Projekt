/*Java för gilla knappen*/
// blogwall_java.js

document.addEventListener("DOMContentLoaded", function () {
    // Hantera gilla-knappar
    document.querySelectorAll(".like-btn").forEach(button => {
        button.addEventListener("click", function () {
            const postId = this.getAttribute("data-post-id");
            const likeCountSpan = this.querySelector(".like-count");

            fetch("../övrigt/like_post.php", {
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
});