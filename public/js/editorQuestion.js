
/**
 * Changes the image preview.
 *
 * @return {void}
 */
function imageChange(){
    validateImage();
    let image = document.getElementById("image").files[0];
    let info = document.getElementById("smallImgInfo");
    let preview = document.getElementById("imagePreview");
    info.innerText = "Datei: " + image.name;
    preview.src = window.URL.createObjectURL(image);
    preview.style.visibility = 'visible';
}

/**
 * Validates an updated image.
 *
 * @return {boolean} valid image?
 */
function validateImage(){
    let image;

    try{
        image = document.getElementById("image").files[0];
    }catch (e){
        if (e instanceof TypeError){ // only ignore an error if we don't have an image (TypeError)
            return true;             // happens when the old image is shown
        }
        return false;
    }

    // This happens when no imgae is uploaded
    if (image == undefined)
        return true;

    // check if it actually is an image
    if (! RegExp('image/.*').test(image.type)){
        let alert = document.getElementById("smallImgAlert");
        alert.style.visibility = 'visible';
        return false;
    }

    //validate image size
    console.log("IMGSIZE: " + image.size);
    if(image.size > 2097152) { //max img size 2mb in binary size
        let alert = document.getElementById("smallImgAlert");
        alert.style.visibility = 'visible';
        return false;
    }else{
        let alert = document.getElementById("smallImgAlert");
        alert.style.visibility = 'hidden';
        return true;
    }
}

/**
 * Vaildate the from, specially if at least one checkbox is checked.
 *
 * All other textfield are validated via html.
 *
 * @return {boolean} is the form valid?
 */
function validateForm(){
    //validate Image
    if(validateImage() == false)
        return false;

    //validate checkboxes
    let checkBoxes = document.getElementsByClassName( 'form-check-input' );
    let isChecked = false;
    for (let i = 0; i < checkBoxes.length; i++){
        if ( checkBoxes[i].checked ){
            isChecked = true;
        };
    };

    if (isChecked == false){
        //checkbox is not valid
        let alert = document.getElementById("smallCheckAlert")
        alert.style.visibility = 'visible';
        return false;
    }

    //nothing is invalid -> form is valid
    return true;
}
