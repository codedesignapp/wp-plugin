// function syncFunction() {
//   // Step 1: Use AJAX to call the backend and fetch the page data
//   const apiKey = document.querySelector('input[name="mnc_api_key"]').value;
//   fetch("http://20.40.53.151:3000/guest/web-builder/project?key=" + apiKey)
//     .then((response) => response.json())
//     .then((response) => {
//       // We have to parse the data
//       const parsedResponse = JSON.parse(response);
//       const { data } = parsedResponse;

//       // Step 2: Parse the returned data to get the keys (page names)
//       const pageNames = Object.keys(data?.blueprint);

//       // Step 3: Send the parsed data to a PHP function in WordPress using AJAX
//       const ajaxUrl = "/wp-admin/admin-ajax.php"; // WordPress AJAX endpoint
//       const formData = new FormData();
//       formData.append("action", "mnc_handle_sync");
//       formData.append("pageNames", JSON.stringify(pageNames));
//       formData.append("fetchedData", JSON.stringify(data)); // Send the entire data object

//       fetch(ajaxUrl, {
//         method: "POST",
//         body: formData,
//       })
//         .then((response) => response.json())
//         .then((result) => {
//           if (result.success) {
//             alert("Sync completed successfully!");
//           } else {
//             alert("Error during sync: " + result.message);
//           }
//         });
//     })
//     .catch((error) => {
//       console.error("Error fetching page data:", error);
//       alert(
//         "Error fetching page data. Please check the console for more details."
//       );
//     });
// }
