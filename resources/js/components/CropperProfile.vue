<template>
</template>

<script>
import VueCropper from "vue-cropperjs";
import "cropperjs/dist/cropper.css";

export default {
  components: {
    VueCropper,
  },
  props: {
    image: {
      required: true
    },

  },
  data() {
    return {
      imgSrc: "/public/images/placeholder.png",
      cropImg: "",
      data: null,
      filename: "",
      mimeType: "",
      modalOpen: false,
    };
  },
  watch: {
    image(value) {
      this.initialImageSetUp(value)
    }
  },
  mounted() {
    this.initialImageSetUp(this.image)
  },
  methods: {
    initialImageSetUp(value) {
      const self = this

      if (!value) {
        this.cropImg = ""
        this.$emit('close', true)
        return;
      }
      this.modalOpen = true
      self.filename = value.name
      self.mimeType = value.type
      self.setImage(value)
    },
async dataURLToFile(imageString, filename, mimeType) {

    const res = await fetch(imageString);
    const blob = await res.blob();
    return new File([blob], filename, { type: mimeType });
  },
    async submitImage() {
      await this.cropImage();
      const imageFileResponse = await this.dataURLToFile(this.cropImg, this.filename, this.mimeType)
      this.$emit('done', imageFileResponse)
      this.modalOpen = false

    },
    cropImage() {
      // get image data for post processing, e.g. upload or setting image src
      this.cropImg = this.$refs.cropper.getCroppedCanvas().toDataURL();
    },
    // flipX() {
    //   const dom = this.$refs.flipX;
    //   let scale = dom.getAttribute("data-scale");
    //   scale = scale ? -scale : -1;
    //   this.$refs.cropper.scaleX(scale);
    //   dom.setAttribute("data-scale", scale);
    // },
    // flipY() {
    //   const dom = this.$refs.flipY;
    //   let scale = dom.getAttribute("data-scale");
    //   scale = scale ? -scale : -1;
    //   this.$refs.cropper.scaleY(scale);
    //   dom.setAttribute("data-scale", scale);
    // },
    getCropBoxData() {
      this.data = JSON.stringify(this.$refs.cropper.getCropBoxData(), null, 4);
    },
    getData() {
      this.data = JSON.stringify(this.$refs.cropper.getData(), null, 4);
    },
    move(offsetX, offsetY) {
      this.$refs.cropper.move(offsetX, offsetY);
    },
    reset() {
      this.$refs.cropper.reset();
    },
    rotate(deg) {
      this.$refs.cropper.rotate(deg);
    },
    setCropBoxData() {
      if (!this.data) return;

      this.$refs.cropper.setCropBoxData(JSON.parse(this.data));
    },
    setData() {
      if (!this.data) return;

      this.$refs.cropper.setData(JSON.parse(this.data));
    },
    setImage(file) {

      if (!file.type.includes("image/")) {
        alert("Please select an image file");
        return;
      }

      if (typeof FileReader === "function") {
        const reader = new FileReader();

        reader.onload = (event) => {
          this.imgSrc = event.target.result;
          // rebuild cropperjs with the updated source
          this.$refs.cropper.replace(event.target.result);
        };

        reader.readAsDataURL(file);
      } else {
        alert("Sorry, FileReader API not supported");
      }
    },
    showFileChooser() {
      this.$refs.input.click();
    },
    zoom(percent) {
      this.$refs.cropper.relativeZoom(percent);
    },
  },
};
</script>

<style>
input[type="file"] {
  display: none;
}

.cropped-image {
  padding: 0 .8rem;
}

.img-cropper {
  max-height: 400px;
  overflow: hidden;
}

</style>