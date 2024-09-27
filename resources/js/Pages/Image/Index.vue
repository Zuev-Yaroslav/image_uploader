<template>
    <div class="bg-white rounded w-3/4 m-4 min-h-screen mx-auto p-4">
        <div class="flex justify-center mb-3">
            <div>
                <input ref="uploadedImagesInput" @change="setImages" type="file" multiple>
            </div>
            <div>
                <a @click.prevent="storeImages" href="#" class="p-2 bg-green-500 text-white rounded">Upload</a>
            </div>
        </div>
        <div class="flex flex-wrap gap-4 items-center">
            <div v-for="imageOnUploadKey in Object.keys(imagesOnUpload)" class="w-20">
                <div class="truncate ...">{{imagesOnUpload[imageOnUploadKey].name}}</div>
                <div>
                    <div :class="'ms-[calc('+imagesOnUpload[imageOnUploadKey].progress+'%-1.25rem)]'" class="inline-block mb-2 py-0.5 px-1.5 bg-blue-50 border border-blue-200 text-xs font-medium text-blue-600 rounded-lg dark:bg-blue-800/30 dark:border-blue-800 dark:text-blue-500">{{imagesOnUpload[imageOnUploadKey].progress}}%</div>
                    <div class="flex w-full h-2 bg-gray-200 rounded-full overflow-hidden dark:bg-neutral-700" role="progressbar" :aria-valuenow="imagesOnUpload[imageOnUploadKey].progress" aria-valuemin="0" aria-valuemax="100">
                        <div :style="{width: imagesOnUpload[imageOnUploadKey].progress + '%'}" class="flex flex-col justify-center rounded-full overflow-hidden bg-blue-600 text-xs text-white text-center whitespace-nowrap transition duration-500 dark:bg-blue-500"></div>
                    </div>
                </div>
                <div v-if="imagesOnUpload[imageOnUploadKey].is_fail" class="flex justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="red" class="size-6">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                    </svg>
                </div>
            </div>
            <div v-for="image in images.data" class="w-28">
                <a :href="image.url" target="_blank">
                    <img :src="image.preview_url" alt="image" class="rounded">
                </a>
                <div class="flex justify-center">
                    <svg v-if="!image.task.is_fail" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="green" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <svg v-if="image.task.is_fail" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="red" class="size-6">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />
                    </svg>


                </div>
            </div>
        </div>
    </div>
</template>

<script>
import axios from "axios";
// import { v4 as uuidv4 } from 'uuid';

export default {
    name: "Index",
    props: {
        images: Object
    },
    data() {
        return {
            entries: {
                images: []
            },
            indexImageIntervalId: null,
            imagesOnUpload: {}
        }
    },
    methods: {
        indexImage() {
            axios.get('/images')
                .then(res => {
                    this.$page.props.images = res.data
                })
        },
        setImages(event) {
            this.entries.images = [];
            for (var i = 0; i < event.target.files.length; i++) {
                this.entries.images.push({uploaded_image: event.target.files[i]})
            }
        },
        stopIndexImageInterval() {
            clearInterval(this.indexImageIntervalId)
        },
        storeImages() {
            this.stopIndexImageInterval()
            let images = this.entries.images
            images.forEach((image) => {
                const index = Math.random().toString(36).substring(2, 40);
                // const index = uuidv4();
                this.imagesOnUpload[index] = {
                    name: image.uploaded_image.name,
                    progress: '0'
                }
                console.log(this.imagesOnUpload);
                axios.post('/images', image, {
                    onUploadProgress: (progressEvent) => {
                        this.imagesOnUpload[index].progress = Math.ceil(progressEvent.progress * 100).toString()
                    },
                    headers: {
                        'Content-Type': 'multipart/form-data'
                    }
                }).then(res => {
                    delete this.imagesOnUpload[index];
                }).catch(error => {
                    if (error.status === 429) {
                        this.imagesOnUpload[index].is_fail = true;
                    }
                })
            })
            this.indexImageIntervalId = setInterval(() => {
                this.indexImage();
            }, 3000)
            this.$refs.uploadedImagesInput.value = null;
            this.entries.images = [];
        }
    }
}
</script>

<style scoped>

</style>
