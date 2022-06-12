<template>
    <div class="sticky-nav">
        <div class="sticky-nav container">
            <div class="icons-section">
                <a href="/"  class="icons-section__icon router-link-exact-active router-link-active">
                    <img
                        src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNSIgaGVpZ2h0PSIyNCIgZmlsbD0ibm9uZSIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xNS41NzcgMy40MTlhMzIuNzIgMzIuNzIgMCAwIDEgNS43NTMgNS44OTMgMS42OCAxLjY4IDAgMCAxLTEuMzM3IDIuNjg5Yy00Ljc4OS0uNjYzLTkuNjQ1LS42NzctMTQuNDM4LS4wNDJsLS4zMTkuMDQyYy0xLjI3NiAwLTEuOTE2LTEuNzg2LTEuMDEzLTIuNjg5bC4zMzctLjQzYy4zOTEtLjQ5OS43OTgtLjk4NCAxLjIxNy0xLjQ1OVY0LjUwMWExLjUgMS41IDAgMCAxIDEuNS0xLjVjLjgxMiAwIDEuNDcuNjQ3IDEuNDk1IDEuNDUzbC4xNDgtLjEzLjczOS0uNjM0Ljk5Ni0uODExYTMgMyAwIDAgMSA0LjI0MyAwbC42NzkuNTR6bS05Ljc2NSA5LjUxNmMyLjI0OS0uMjkzIDQuNTM4LS40NDMgNi44MDYtLjQ0M2E1Mi43NyA1Mi43NyAwIDAgMSA3LjEyNi40ODZjLjA1NCAyLjExNS0uMDMgNC4yMzItLjI5MyA2LjMzNWEyLjc1IDIuNzUgMCAwIDEtMi4zMzkgMi4zOGMtMi44NzYuNDExLTUuNzk1LjQxMS04LjY3IDBhMi43NSAyLjc1IDAgMCAxLTIuMzM5LTIuMzhjLS4yNjQtMi4xMTctLjM0OC00LjI0OS0uMjkxLTYuMzc4em03LjM3NSA3LjA2NmMuNjMzIDAgMS4xOTUtLjQ1OSAxLjM5NS0xLjE0YTQuODYgNC44NiAwIDAgMCAwLTIuNzIxYy0uMi0uNjgxLS43NjItMS4xNC0xLjM5NS0xLjE0aC0uODIxYy0uNjMzIDAtMS4xOTUuNDU5LTEuMzk1IDEuMTQtLjI2Ljg4My0uMjYgMS44MzggMCAyLjcyMS4yLjY4MS43NjIgMS4xNCAxLjM5NSAxLjE0aC44MjF6IiBmaWxsPSIjMDA5NWRhIi8+PC9zdmc+"
                        alt="home" class="icons-section__icon__image"> <span
                        class="icons-section__icon__text icons-section__icon__text--selected">
                        Beranda
                    </span>
                </a>
                <a href="/categories" class="icons-section__icon">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNSIgaGVpZ2h0PSIyNCIgZmlsbD0ibm9uZSIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik00Ljg2MSAxMS4xNTJhOS4yOSA5LjI5IDAgMCAwIDQuNTA3IDAgMyAzIDAgMCAwIDIuMTgzLTIuMTgzYy4zNy0xLjQ4LjM3LTMuMDI4IDAtNC41MDhhMyAzIDAgMCAwLTIuMTgzLTIuMTgzIDkuMjkgOS4yOSAwIDAgMC00LjUwNyAwIDMgMyAwIDAgMC0yLjE4MyAyLjE4M2MtLjM3IDEuNDgtLjM3IDMuMDI4IDAgNC41MDhhMyAzIDAgMCAwIDIuMTgzIDIuMTgzem0xNC45MzUtOC44NzNhMyAzIDAgMCAxIDIuMTgzIDIuMTgzIDkuMjkgOS4yOSAwIDAgMSAwIDQuNTA3IDMgMyAwIDAgMS0yLjE4MyAyLjE4M2MtMS40OC4zNy0zLjAyOC4zNy00LjUwOCAwYTMgMyAwIDAgMS0yLjE4My0yLjE4MyA5LjI5IDkuMjkgMCAwIDEgMC00LjUwNyAzIDMgMCAwIDEgMi4xODMtMi4xODNjMS40OC0uMzcgMy4wMjgtLjM3IDQuNTA4IDB6TTkuMzY4IDIxLjU4YTkuMjkgOS4yOSAwIDAgMS00LjUwNyAwIDMgMyAwIDAgMS0yLjE4My0yLjE4M2MtLjM3LTEuNDgtLjM3LTMuMDI4IDAtNC41MDhhMyAzIDAgMCAxIDIuMTgzLTIuMTgzIDkuMjkgOS4yOSAwIDAgMSA0LjUwNyAwIDMgMyAwIDAgMSAyLjE4MyAyLjE4M2MuMzcgMS40OC4zNyAzLjAyOCAwIDQuNTA4YTMgMyAwIDAgMS0yLjE4MyAyLjE4M3ptNS45MjEgMGE5LjI5IDkuMjkgMCAwIDAgNC41MDcgMCAzIDMgMCAwIDAgMi4xODMtMi4xODNjLjM3LTEuNDguMzctMy4wMjggMC00LjUwOGEzIDMgMCAwIDAtMi4xODMtMi4xODMgOS4yOSA5LjI5IDAgMCAwLTQuNTA3IDAgMyAzIDAgMCAwLTIuMTgzIDIuMTgzYy0uMzcgMS40OC0uMzcgMy4wMjggMCA0LjUwOGEzIDMgMCAwIDAgMi4xODMgMi4xODN6IiBmaWxsPSIjODU4NTg1Ii8+PC9zdmc+"
                        alt="category" class="icons-section__icon__image"> <span
                        class="icons-section__icon__text">
                        Kategori
                    </span>
                </a>
                <a href="/cart" class="icons-section__icon">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNCIgaGVpZ2h0PSIyNCIgZmlsbD0ibm9uZSIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xOC42NzUgMy4wMTJjMS40LS4xNDMgMi41NTUuOTkgMi4zMDMgMi4yNmwtLjMzNCAxLjY3N2MtLjgzNiA0LjIwOS0uNzQ0IDguNTI5LjI2OSAxMi43MDUuMjkyIDEuMjAzLS43MDggMi4zNDUtMi4wNTQgMi4zNDVINS4xNjVjLTEuMzQ2IDAtMi4zNDYtMS4xNDEtMi4wNTQtMi4zNDVhMjYuNzggMjYuNzggMCAwIDAgMC0xMi42NTVsLS4wNi0uMjQ5Yy0uMjcyLTEuMTIuNTc4LTIuMjA3IDEuODIyLTIuMzM0bDEzLjgwMi0xLjQwNHptLTIuMTYzIDUuOTg3YTEgMSAwIDEgMC0yIDAgMSAxIDAgMSAwIDIgMHptLTgtMWExIDEgMCAxIDEgMCAyIDEgMSAwIDEgMSAwLTJ6bS0xLjI1IDRjMCAyLjYxOSAyLjEzMSA0Ljc1IDQuNzUgNC43NXM0Ljc1LTIuMTMgNC43NS00Ljc1YS43NS43NSAwIDEgMC0xLjUgMGMwIDEuNzkyLTEuNDU4IDMuMjUtMy4yNSAzLjI1cy0zLjI1LTEuNDU4LTMuMjUtMy4yNWEuNzUuNzUgMCAxIDAtMS41IDB6IiBmaWxsPSIjODU4NTg1Ii8+PC9zdmc+"
                        alt="cart" class="icons-section__icon__image">
                    <span class="icons-section__icon__text">
                        Keranjang
                    </span>
                    <span class="icons-section__icon__cart-quantity">
                        0
                    </span>
                </a>
                <a to="/official-store" href="/official-store" class="icons-section__icon">
                    <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNSIgaGVpZ2h0PSIyNCIgZmlsbD0ibm9uZSIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBkPSJNMTcuODkyIDYuMjY1YTMuMTggMy4xOCAwIDAgMC0yLjc1NS0xLjYwMWgtNS4wNzJjLTEuMTM3IDAtMi4xODcuNjEtMi43NTUgMS42MDFsLTIuNSA0LjM2MWEzLjIyIDMuMjIgMCAwIDAgLjA1OCAzLjI5NWwyLjQyNiAzLjkwNmMuNTgxLjkzNiAxLjYgMS41MDQgMi42OTcgMS41MDRoNS4yMmMxLjA5NyAwIDIuMTE2LS41NjggMi42OTctMS41MDRsMi40MjYtMy45MDZhMy4yMiAzLjIyIDAgMCAwIC4wNTgtMy4yOTVsLTIuNS00LjM2MXpNMTUuMzUgMmE1LjU2IDUuNTYgMCAwIDEgNC44MjEgMi44MDJsMi42ODYgNC42ODVjMS4wMjggMS43OTQuOTg5IDQuMDExLS4xMDUgNS43NjdsLTIuNTU2IDQuMTE1Yy0xLjAxNyAxLjYzNy0yLjggMi42MzItNC43MTkgMi42MzJIOS43MjFhNS41NiA1LjU2IDAgMCAxLTQuNzE5LTIuNjMybC0yLjU1Ni00LjExNWMtMS4wOS0xLjc1Ni0xLjEyOS0zLjk3NC0uMTAxLTUuNzY3bDIuNjg2LTQuNjg1QTUuNTYgNS41NiAwIDAgMSA5Ljg1MiAyaDUuNDk4em0tLjIxMiAxLjY2M2gtNS4wNzJhNC4xOSA0LjE5IDAgMCAwLTMuNjIyIDIuMTA0bC0yLjUgNC4zNjFhNC4yMSA0LjIxIDAgMCAwIC4wNzYgNC4zMmwyLjQyNiAzLjkwNmE0LjE1IDQuMTUgMCAwIDAgMy41NDYgMS45NzZoNS4yMmE0LjE1IDQuMTUgMCAwIDAgMy41NDYtMS45NzZsMi40MjYtMy45MDZhNC4yMSA0LjIxIDAgMCAwIC4wNzYtNC4zMmwtMi41LTQuMzYxYy0uNzQ0LTEuMjk4LTIuMTMyLTIuMTA0LTMuNjIyLTIuMTA0em0xLjg4MyA0LjUxNGMuNTE5LjM3Ni42MzEgMS4wOTkuMjUgMS42MTRsLTQuNjY2IDYuMzgyYTEuMTcgMS4xNyAwIDAgMS0uODUyLjQ2OGwtLjA4OS4wMDNjLS4zMDggMC0uNjA1LS4xMjEtLjgyNS0uMzM4bC0zLjUtMi45OGMtLjQ1Ni0uNDUxLS40NTYtMS4xODIgMC0xLjYzM3MxLjE5NC0uNDUxIDEuNjUgMGwyLjUzOSAyLjAyOCAzLjg2Mi01LjI5NmMuMzgyLS41MTQgMS4xMTEtLjYyNSAxLjYzMS0uMjQ4eiIgZmlsbD0iIzg1ODU4NSIvPjwvc3ZnPg=="
                        alt="officialStore" class="icons-section__icon__image">
                        <span class="icons-section__icon__text">
                        Official Store
                    </span>
                </a>
                <a href="/account" class="icons-section__icon">
                <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyNSIgaGVpZ2h0PSIyNCIgZmlsbD0ibm9uZSIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBmaWxsLXJ1bGU9ImV2ZW5vZGQiIGQ9Ik0xNi42OTkgNi41YTQuNSA0LjUgMCAwIDEtOSAwIDQuNSA0LjUgMCAwIDEgOSAwem0zLjQ4NSAxMi4wODlhMTcuODEgMTcuODEgMCAwIDAtMS43NDktNi4wOTljLS43MjQtMS40NjMtMi4zNzktMS45MTctMy43My0xLjE1M2E1LjA3IDUuMDcgMCAwIDEtNS4wMSAwYy0xLjM1MS0uNzY0LTMuMDA2LS4zMS0zLjczIDEuMTUzLS45MjggMS44NzUtMS41MyAzLjk0Mi0xLjc0OSA2LjA5OS0uMTM4IDEuMzU5LjY2MSAyLjYzIDEuODgxIDIuOTI4YTI1LjYxIDI1LjYxIDAgMCAwIDEyLjIwOCAwYzEuMjE4LS4yOTkgMi4wMTctMS41NjkgMS44NzktMi45Mjh6TTExLjE5OSAxNWExIDEgMCAxIDAgMiAwIDEgMSAwIDEgMC0yIDB6bTAgM2ExIDEgMCAxIDAgMiAwIDEgMSAwIDEgMC0yIDB6IiBmaWxsPSIjODU4NTg1Ii8+PC9zdmc+"
                    alt="account" class="icons-section__icon__image"> <span data-v-51011145=""
                        class="icons-section__icon__text">
                        Akun
                    </span>
                </a>
            </div>
        </div>
    </div>
</template>