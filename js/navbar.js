document.addEventListener("DOMContentLoaded", () => {

    const logoutButton =
        document.getElementById("logout-btn");


    if (!logoutButton) {
        return;
    }


    logoutButton.addEventListener(
        "click",
        async () => {

            await fetch(
                "api/auth/logout.php",
                {
                    method: "POST",
                    credentials: "same-origin"
                }
            );


            Auth.clearUserCache();


            window.location.href = "index.php";

        }
    );

});