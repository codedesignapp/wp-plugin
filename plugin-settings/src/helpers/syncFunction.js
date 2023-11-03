// async function syncFunction(apiKey) {
//   try {
//     const response = await fetch(
//       "http://20.40.53.151:3000/guest/web-builder/project?wordpress=true&key=" +
//         apiKey
//     );
//     const responseData = await response.json();

//     const parsedResponse = JSON.parse(responseData);
//     const { data } = parsedResponse;
//     const pageNames = Object.keys(data?.blueprint);

//     const ajaxUrl = "/wp-admin/admin-ajax.php";
//     const formData = new FormData();
//     formData.append("action", "mnc_handle_sync");
//     formData.append("pageNames", JSON.stringify(pageNames));
//     formData.append("fetchedData", JSON.stringify(data));

//     const syncResponse = await fetch(ajaxUrl, {
//       method: "POST",
//       body: formData,
//     });
//     const syncResult = await syncResponse.json();

//     if (syncResult.success) {
//       alert("Sync completed successfully!");
//       return true;
//     } else {
//       console.error("Error during sync:", syncResult.message);
//       return false;
//     }
//   } catch (error) {
//     console.error("Error during sync:", error);
//     return false;
//   }
// }

// export default syncFunction;
