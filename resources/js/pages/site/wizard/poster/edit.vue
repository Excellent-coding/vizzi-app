<template>
<div>
    <b-row>
        <b-header
            title="Poster"
            :selectAll="selectAll"
            :isSelectedAll="isSelectedAll"
            :keymap="keymap"
            :isAnyItemSelected="isAnyItemSelected"
            :setStatus="setStatus"
            :initData="initData"
            :status="status"
            :type="type"
        />
    </b-row>

    <template v-if="type && type != 'type'">
        <div class="arrow-btn" @click="returnTab" >
            Return
        </div>
        <div class="arrow-btn" style="right: 50px" @click="nextTab" v-if="type != 'settings'">
            Next
        </div>
        <template v-else>
            <router-link tag="div" :to="'/app/wizard/poster/preview/' + id" class="arrow-btn" style="right: 120px" @click="publish">
                Preview
            </router-link>
            <div class="arrow-btn" style="right: 50px" @click="publish">
                Publish
            </div>
        </template>

    </template>
    
    <b-tabs v-model="tabIndex" class="mx-4">
        <b-tab title="Type" @click="type = 'type'">
            <poster-type :goNext="goNext" :viewType="viewType" @setType="setType" />
        </b-tab>
        <b-tab title="Poster Preview" @click="type = 'upload'">
            <template v-if="viewType < 3">
                <div class="py-5 bg" :style="getBackdrop(posterItem)">
                    <div class="view-header mx-auto mb-4 text-center" @click="titleEdit = true" v-if="!titleEdit">{{posterItem.title}}</div>
                    <div class="view-header mx-auto mb-4 text-center d-flex" v-else>
                        <input v-model="posterItem.title" class="ml-auto mr-1" />
                        <b-button variant="primary default" class="mr-auto ml-1" @click="titleEdit = false">Update</b-button>
                    </div>
                    <div v-if="viewType == 1">
                        <div v-if="items.length" class="text-center">
                            <div class="single-view mx-auto position-relative">
                                <template v-if="!changeUrl">
                                    <img :src="'/data/' + items[0]" class="w-100 h-100 object-fit" v-if="fileType(items[0]) !== 'mp4' && fileType(items[0]) !== 'pdf'" />
                                    <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(items[0]) == 'mp4'">
                                        <source class="w-100 h-100 object-fit" :src="/data/ + items[0]" type="video/mp4">
                                    </video>
                                    <iframe class="poster-item w-100 h-100 object-fit" :src="'/data/'+items[0]" v-else-if="fileType(items[0]) == 'pdf'" />
                                    <div class="position-center position-absolute">
                                        <button class="btn btn-primary mx-1" @click="upload">Save Changes</button>
                                        <label for="media" class="btn btn-outline-primary mx-1 mt-2">Change</label>
                                    </div>
                                </template>
                                <template v-else>
                                    <img :src="changeUrl" class="w-100 h-100 object-fit" v-if="extension !== 'mp4' && extension !== 'pdf'" />
                                    <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="extension === 'mp4'">
                                        <source class="w-100 h-100 object-fit" :src="changeUrl" type="video/mp4">
                                    </video>
                                    <iframe class="w-100 h-100 object-fit" :src="changeUrl" v-else-if="extension === 'pdf'" />
                                    <div class="position-center position-absolute">
                                        <button class="btn btn-primary mx-1" @click="upload">Save Changes</button>
                                        <label for="media" class="btn btn-outline-primary mx-1 mt-2">Change</label>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div v-else class="text-center mx-auto">
                            <div class="single-view mx-auto position-relative" v-if="!url">
                                <label for="media" class="btn btn-outline-primary position-center position-absolute">Upload</label>
                            </div>
                            <div v-else class="single-view mx-auto position-relative">
                                <img :src="url" class="w-100 h-100 object-fit" v-if="extension !== 'mp4' && extension !== 'pdf'" />
                                <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="extension === 'mp4'">
                                    <source class="w-100 h-100 object-fit" :src="url" type="video/mp4">
                                </video>
                                <div class="w-100 h-100" v-else-if="extension === 'pdf'">
                                    <iframe class="w-100 h-100 object-fit" :src="url" />
                                </div>
                                <div class="position-center position-absolute">
                                    <button class="btn btn-primary mx-1" @click="upload">Confirm</button>
                                    <label for="media" class="btn btn-outline-primary mx-1 mt-2">Change</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div v-else-if="viewType == 2" class="text-center">
                        <div class="multiple-view d-flex justify-content-end align-items-center">
                            <div v-for="(item, index) in items" :key="item.id" class="item mx-auto bg-white position-relative">
                                <template v-if="isChanged(index)">
                                    <img :src="getChangeUrl(index)" class="w-100 h-100 object-fit" v-if="getExtension(index) !== 'mp4' && getExtension(index) !== 'pdf'" />
                                    <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="getExtension(index) === 'mp4'">
                                        <source class="w-100 h-100 object-fit" :src="getChangeUrl(index)" type="video/mp4">
                                    </video>
                                    <iframe class="w-100 h-100 object-fit" :src="getChangeUrl(index)" v-else-if="getExtension(index) === 'pdf'" />
                                    <div class="position-center position-absolute">
                                        <label for="media" class="btn btn-outline-primary mx-1 mt-2" @click="currentIndex = index">Change</label>
                                    </div>
                                </template>
                                <template v-else>
                                    <img :src="'/data/' + item" class="w-100 h-100 object-fit" v-if="fileType(item) !== 'mp4' && fileType(item) !== 'pdf'" />
                                    <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(item) == 'mp4'">
                                        <source class="w-100 h-100 object-fit" :src="/data/ + item" type="video/mp4">
                                    </video>
                                    <iframe class="w-100 h-100 item" :src="'/data/' + item" v-else />
                                    <div class="position-center position-absolute">
                                        <label for="media" class="btn btn-primary mx-1 mt-2" @click="currentIndex = index">Change</label>
                                    </div>
                                </template>
                            </div>
                            <div v-for="index in length" :key="index" class="item mx-auto bg-white position-relative">
                                <template v-if="isUploaded(items.length + index - 1)">
                                    <img :src="getUrl(items.length + index - 1)" class="w-100 h-100 object-fit" v-if="getExtension(items.length + index - 1) !== 'mp4' && getExtension(items.length + index - 1) !== 'pdf'" />
                                    <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="getExtension(items.length + index - 1) == 'mp4'">
                                        <source class="w-100 h-100 object-fit" :src="getUrl(items.length + index - 1)" type="video/mp4">
                                    </video>
                                    <iframe class="w-100 h-100 item" :src="getUrl(items.length + index - 1)" v-else-if="getExtension(items.length + index - 1) === 'pdf'" />
                                    <div class="position-center position-absolute">
                                        <label for="media" class="btn btn-primary mx-1 mt-2" @click="currentIndex = items.length + index - 1">Change</label>
                                    </div>
                                </template>
                                <label 
                                    for="media" 
                                    class="btn btn-outline-primary position-center position-absolute"
                                    v-else
                                    @click="currentIndex = items.length + index - 1"
                                >
                                    Upload
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn-primary mt-2" v-if="viewType == 2" @click="multipleUpload">Submit</button>
            </template>
            <template v-else>
                <b-row>
                    <b-colxx xxs="3">
                        <div class="border">
                            <b-button v-b-toggle.headerSet block variant="primary" class="mt-2">Header</b-button>
                            <b-collapse id="headerSet">
                                <div class="my-2">
                                    <b-form-group label="Title" class="has-float-label my-4">
                                        <b-form-input type="text" v-model="posterItem.title" />
                                    </b-form-group>
                                    <b-row>
                                        <b-colxx xs="12" md="6">
                                            <b-form-group label="X(%)" class="has-float-label">
                                                <b-form-input type="number" v-model="posterHeader.position.x" />
                                            </b-form-group>
                                        </b-colxx>
                                        <b-colxx xs="12" md="6">
                                            <b-form-group label="Y(%)" class="has-float-label">
                                                <b-form-input type="number" v-model="posterHeader.position.y" />
                                            </b-form-group>
                                        </b-colxx>
                                        <b-colxx xs="12" md="6">
                                            <b-form-group label="eX(%)" class="has-float-label">
                                                <b-form-input type="number" v-model="posterHeader.position.ex" />
                                            </b-form-group>
                                        </b-colxx>
                                        <b-colxx xs="12" md="6">
                                            <b-form-group label="eY(%)" class="has-float-label">
                                                <b-form-input type="number" v-model="posterHeader.position.ey" />
                                            </b-form-group>
                                        </b-colxx>
                                        <b-colxx xs="12" md="6">
                                            <b-form-group label="Background" class="has-float-label">
                                                <b-form-input type="color" v-model="posterHeader.bg" />
                                            </b-form-group>
                                        </b-colxx>
                                        <b-colxx xs="12" md="6">
                                            <b-form-group label="TextColor" class="has-float-label">
                                                <b-form-input type="color" v-model="posterHeader.text.color" />
                                            </b-form-group>
                                        </b-colxx>
                                        <b-colxx xs="12" md="6">
                                            <b-form-group label="FontSize" class="has-float-label">
                                                <b-form-input type="number" v-model="posterHeader.text.size" />
                                            </b-form-group>
                                        </b-colxx>
                                    </b-row>
                                </div>
                            </b-collapse>
                        </div>
                        <div class="border">
                            <b-button v-b-toggle.contentSet block variant="primary">Content</b-button>
                            <b-collapse id="contentSet">
                                <div class="d-flex justify-content-end align-items-center my-1">
                                    <b-button class="mr-auto" variant="primary default" @click="addLayer">Add Layer</b-button>
                                    <b-button class="ml-auto" variant="secondary default" @click="posterContent = []">Reset</b-button>
                                </div>
                                <div v-for="(content, index) in posterContent" :key="index">
                                    <i class="simple-icon-trash float-right text-danger mt-2 cursor-pointer" @click="del(index)"></i>
                                    <b-button v-b-toggle="'content_' + index" variant="link">Layer {{index + 1}}</b-button>
                                    <b-collapse :id="'content_' + index" >
                                        <b-row class="my-2">
                                            <b-colxx xs="12">
                                                <b-form-group label="Title" class="has-float-label">
                                                    <b-form-input type="text" v-model="content.title" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="TextColor" class="has-float-label">
                                                    <b-form-input type="color" v-model="content.header.color" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="FontSize" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.header.size" />
                                                </b-form-group>
                                            </b-colxx>
                                        </b-row>
                                        <b-row class="my-2">
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="X(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.position.x" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Y(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.position.y" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="eX(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.position.ex" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="eY(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.position.ey" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Background" class="has-float-label">
                                                    <b-form-input type="color" v-model="content.bg" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Padding" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.padding" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Radius" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.borderRadius" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="zIndex" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.zIndex" />
                                                </b-form-group>
                                            </b-colxx>
                                        </b-row>
                                        <b-row class="my-2">
                                            <b-colxx xs="12">
                                                <b-form-group label="Type" class="has-float-label">
                                                    <b-select class="form-control" v-model="content.type">
                                                        <option value="text">Text</option>
                                                        <option value="media">Media</option>
                                                    </b-select>
                                                </b-form-group>
                                            </b-colxx>
                                        </b-row>
                                        <b-row class="my-2" v-if="content.type == 'text'">
                                            <b-colxx xs="12">
                                                <b-form-group label="Body" class="has-float-label">
                                                    <b-form-textarea type="text" v-model="content.text" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="TextColor" class="has-float-label">
                                                    <b-form-input type="color" v-model="content.body.color" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="FontSize" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.body.size" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="X(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.body.position.x" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Y(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.body.position.y" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="eX(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.body.position.ex" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="eY(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.body.position.ey" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Background" class="has-float-label">
                                                    <b-form-input type="color" v-model="content.body.bg" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="BorderRadius" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.body.borderRadius" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Padding" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.body.padding" />
                                                </b-form-group>
                                            </b-colxx>
                                        </b-row>
                                        <b-row class="my-2" v-else>
                                            <b-colxx xs="12" />
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="X(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.media.position.x" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Y(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.media.position.y" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="eX(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.media.position.ex" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="eY(%)" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.media.position.ey" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="BorderRadius" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.media.borderRadius" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Padding" class="has-float-label">
                                                    <b-form-input type="number" v-model="content.media.padding" />
                                                </b-form-group>
                                            </b-colxx>
                                            <b-colxx xs="12" md="6">
                                                <b-form-group label="Background" class="has-float-label">
                                                    <b-form-input type="color" v-model="content.media.bg" />
                                                </b-form-group>
                                            </b-colxx>
                                        </b-row>
                                    </b-collapse>
                                </div>
                            </b-collapse>
                        </div>
                        <b-button variant="secondary" class="float-right mt-2" @click="advancedUpload">Submit</b-button>
                    </b-colxx>
                    <b-colxx xxs="9">
                        <div class="bg position-fixed" :style="getBackdropType(posterItem)">
                            <div class="position-absolute" :style="getPosterHeaderStyle(posterHeader)">{{posterItem.title}}</div>
                            <div class="position-absolute position-relative" v-for="(content, index) in posterContent" :key="index" :style="contentStyle(content)">
                                <div :style="contentHeaderStyle(content.header)">{{content.title}}</div>
                                <div :style="contentBodyStyle(content.body)" v-if="content.type == 'text'">{{content.text}}</div>
                                <div :style="contentMediaStyle(content.media)" v-else>
                                    <template v-if="items.length > index">
                                        <template v-if="isChanged(index)">
                                            <img :src="getChangeUrl(index)" class="w-100 h-100 object-fit" v-if="getExtension(index) !== 'mp4' && getExtension(index) !== 'pdf'" />
                                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="getExtension(index) === 'mp4'">
                                                <source class="w-100 h-100 object-fit" :src="getChangeUrl(index)" type="video/mp4">
                                            </video>
                                            <iframe class="w-100 h-100 object-fit" :src="getChangeUrl(index)" v-else-if="getExtension(index) === 'pdf'" />
                                            <div class="position-center position-absolute">
                                                <label for="media" class="btn btn-outline-primary mx-1 mt-2" @click="currentIndex = index">Change</label>
                                            </div>
                                        </template>
                                        <template v-if="!isChanged(index)">
                                            <img :src="'/data/' + items[index]" class="w-100 h-100 object-fit" v-if="fileType(items[index]) !== 'mp4' && fileType(items[index]) !== 'pdf'" />
                                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(items[index]) == 'mp4'">
                                                <source class="w-100 h-100 object-fit" :src="/data/ + items[index]" type="video/mp4">
                                            </video>
                                            <iframe class="w-100 h-100 item" :src="'/data/' + items[index]" v-else />
                                            <div class="position-center position-absolute">
                                                <label for="media" class="btn btn-primary mx-1 mt-2" @click="currentIndex = index">Change</label>
                                            </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <template v-if="isUploaded(index)">
                                            <img :src="getUrl(index)" class="w-100 h-100 object-fit" v-if="getExtension(index) !== 'mp4' && getExtension(index) !== 'pdf'" />
                                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="getExtension(index) == 'mp4'">
                                                <source class="w-100 h-100 object-fit" :src="getUrl(index)" type="video/mp4">
                                            </video>
                                            <iframe class="w-100 h-100 item" :src="getUrl(index)" v-else-if="getExtension(index) === 'pdf'" />
                                            <div class="position-center position-absolute">
                                                <label for="media" class="btn btn-primary mx-1 mt-2" @click="currentIndex = index">Change</label>
                                            </div>
                                        </template>
                                        <template v-else-if="content.media.file">
                                            <img :src="'/data/'+content.media.file" class="w-100 h-100 object-fit" v-if="fileType(content.media.file) !== 'mp4' && fileType(content.media.file) !== 'pdf'" />
                                            <video width="100%" height="100%" muted="" autoplay="autoplay" loop="loop" v-else-if="fileType(content.media.file) == 'mp4'">
                                                <source class="w-100 h-100 object-fit" :src="'/data/'+content.media.file" type="video/mp4">
                                            </video>
                                            <iframe class="w-100 h-100 item" :src="'/data/'+content.media.file" v-else-if="fileType(content.media.file) === 'pdf'" />
                                            <div class="position-center position-absolute">
                                                <label for="media" class="btn btn-primary mx-1 mt-2" @click="currentIndex = index">Change</label>
                                            </div>
                                        </template>
                                        <label 
                                            for="media" 
                                            class="btn btn-outline-primary position-center position-absolute"
                                            v-else
                                            @click="currentIndex = index"
                                        >
                                            Upload
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </b-colxx>
                </b-row>
            </template>
        </b-tab>
        <b-tab title="Poster Menu" @click="type = 'modal'">
            <b-row>
                <b-colxx xs="12" md="6">
                    <h3 class="text-center mt-4">Pages</h3>
                    <draggable class="list-group" :list="header" group="menus" @change="getHeader">
                        <b-colxx xxs="12" class="mb-3" v-for="(item, index) in header" :key="index" :id="item.id">
                            <b-card :class="{'d-flex flex-row':true,'active' : selectedMenus.includes(item.id)}" no-body>
                                <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                    <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event, item.id)">
                                        <b-form-checkbox :checked="selectedMenus.includes(item.id)" class="itemCheck mb-0" />
                                    </div>
                                    <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                        <b-button variant="link">
                                            <p class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</p>
                                        </b-button>
                                        <div class="w-15 w-sm-100">
                                            <b-badge pill :variant="'primary'">Header</b-badge>
                                        </div>
                                    </div>
                                    <div class="my-auto mr-2">
                                        <button
                                            type="button"
                                            class="btn btn-primary mb-1"
                                            v-b-modal.add_modal
                                            @click="edit(item)"
                                        >Edit</button>
                                    </div>
                                </div>
                            </b-card>
                        </b-colxx>
                    </draggable> 
                </b-colxx>
                <b-colxx xs="12" md="6">
                    <h3 class="text-center mt-4">Page Assets (Non Menu Items)</h3>
                    <draggable class="list-group" :list="menus" group="menus" @change="getHeader">
                        <b-colxx xxs="12" class="mb-3" v-for="(item, index) in menus" :key="index" :id="item.id">
                            <b-card :class="{'d-flex flex-row':true,'active' : selectedMenus.includes(item.id)}" no-body>
                                <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                    <div class="custom-control custom-checkbox pl-1 align-self-center pr-4" @click.prevent="toggleItem($event, item.id)">
                                        <b-form-checkbox :checked="selectedMenus.includes(item.id)" class="itemCheck mb-0" />
                                    </div>
                                    <div class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                                        <b-button variant="link">
                                            <p class="list-item-heading mb-0 truncate">{{capitalizeFirstLetter(item.title)}}</p>
                                        </b-button>
                                        <div class="w-15 w-sm-100">
                                            <b-badge pill :variant="'danger'">Inactive</b-badge>
                                        </div>
                                    </div>
                                    <div class="my-auto mr-2">
                                        <button
                                            type="button"
                                            class="btn btn-primary mb-1"
                                            v-b-modal.add_modal
                                            @click="edit(item)"
                                        >Edit</button>
                                    </div>
                                </div>
                            </b-card>
                        </b-colxx>
                    </draggable> 
                </b-colxx>
            </b-row>
        </b-tab>
        <b-tab title="Text Box Sizes" @click="type = 'content'">
            <div class="wizard-basic-step mt-2">
                <b-tabs v-model="tabMenu">
                    <b-tab v-for="(menu, index) in header" :key="menu.id" :title="menu.title" @click="getTab(menu.id, index)">
                        <b-card no-body class="pt-3">
                            <b-tabs pills card v-model="tabModel">
                                <b-tab v-for="(tab, key) in tabs[tabMenu]" :key="tab.id" @click="contentIndex = key">
                                    <template v-slot:title>
                                        {{tab.title}}
                                    </template>
                                    <div class="d-flex justify-content-end align-items-center mb-2">
                                        <div class="my-auto mr-2" style="font-weight: bold">Tab Title</div>
                                        <b-form-input v-model="tab.title" style="width: 120px" class="mr-2" />
                                        <b-button variant="primary" size="sm" class="mr-auto" @click="updateTitle(key)">Update</b-button>
                                        <b-button size="sm" variant="danger" class="ml-auto" @click="closeTab()" v-if="tab.length != 1">
                                            Close tab
                                        </b-button>
                                    </div>
                                    <b-quill-editor
                                        :onEditorChange="onEditorChange"
                                    />
                                    <b-row>
                                        <b-colxx xxs="12">
                                            <div class="top-right-button-container d-flex my-2">
                                                <b-button variant="primary" size="sm" v-b-modal.asset_modal>Add Asset Modal</b-button>
                                                <b-status-button
                                                    :selectAll="selectAll"
                                                    :isSelectedAll="isSelectedAll" 
                                                    :keymap="keymap"
                                                    :isAnyItemSelected="isAnyItemSelected" 
                                                    :setStatus="setStatus"
                                                    :status="status"
                                                />
                                            </div>
                                        </b-colxx>
                                        <b-colxx xxs="12">
                                            <b-card class="d-flex flex-row mb-3" no-body v-for="(item, index) in assets" :key="index" :id="item.id">
                                                <div class="custom-control custom-checkbox pl-1 align-self-center pr-4">
                                                    <div class="custom-control custom-checkbox mb-0" @click.prevent="toggleItem($event, item.id)">
                                                        <b-form-checkbox :checked="selectedAssets.includes(item.id)" />
                                                    </div>
                                                </div>
                                                <img class="list-thumbnail responsive border-0" :src="getType(item.item)">
                                                <div class="pl-2 d-flex flex-grow-1 min-width-zero">
                                                    <div
                                                        class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center"
                                                    >
                                                        <p class="list-item-heading mb-1 truncate">{{item.title}}</p>
                                                        <div class="w-15 w-sm-100">
                                                            <b-badge variant="primary" pill v-if="item.status == 1">Active</b-badge>
                                                            <b-badge variant="danger" pill v-else>Inactive</b-badge>
                                                        </div>
                                                    </div>
                                                    <div class="my-auto mr-2">
                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-primary mb-1"
                                                            v-b-modal.view_modal
                                                            @click="view(item)"
                                                        >View</button>
                                                        <button
                                                            type="button"
                                                            class="btn btn-primary mb-1"
                                                            v-b-modal.asset_modal
                                                            @click="edit(item)"
                                                        >Edit</button>
                                                    </div>
                                                </div>
                                            </b-card>
                                        </b-colxx>
                                    </b-row>
                                </b-tab>
                                <template v-slot:tabs-end>
                                    <b-nav-item role="presentation" @click.prevent="addTab(index)" href="#"><b>+</b></b-nav-item>
                                </template>
                            </b-tabs>
                        </b-card>
                    </b-tab>
                </b-tabs>
            </div>
            <button class="btn btn-primary mt-2" @click="setContent">Save</button>
        </b-tab>
        <b-tab title="Poster Menu Settings" @click="type = 'settings'">
            <b-row>
                <b-col class="header text-center w-100" :style="getHeaderStyle()">
                    <div class="d-inline-flex">
                        <div class="header-btn" v-for="(item, index) in header" :key="index" :style="getButtonStyle()">{{item.title}}</div>
                    </div>
                </b-col>
            </b-row>

            <div class="my-4">
                <h6>Background</h6>
                <b-row>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="color" class="form-control" v-model="headerStyle.bg" />
                            <span>Header Background</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="number" class="form-control" v-model="headerStyle.height" />
                            <span>Header Height</span>
                        </label>
                    </b-colxx>
                </b-row>
            </div>
            <div class="mb-4">
                <h6>Button</h6>
                <b-row>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <b-select class="form-control" v-model="headerStyle.btn.hasBg">
                                <option :value="true">True</option>
                                <option :value="false">None</option>
                            </b-select>
                            <span>Background</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="color" class="form-control" v-model="headerStyle.btn.bg" />
                            <span>Background</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="color" class="form-control" v-model="headerStyle.btn.color" />
                            <span>Text Color</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <b-select class="form-control" v-model="headerStyle.btn.boxShadow">
                                <option :value="true">True</option>
                                <option :value="false">None</option>
                            </b-select>
                            <span>Box Shadow</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="number" class="form-control" v-model="headerStyle.btn.fontSize" />
                            <span>Font Size</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="number" class="form-control" v-model="headerStyle.btn.radius" />
                            <span>Boder radius</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="number" class="form-control" v-model="headerStyle.btn.space" />
                            <span>Space</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="number" class="form-control" v-model="headerStyle.btn.paddingX" />
                            <span>PaddingX</span>
                        </label>
                    </b-colxx>
                    <b-colxx xs="12" md="3">
                        <label class="form-group has-float-label">
                            <input type="number" class="form-control" v-model="headerStyle.btn.paddingY" />
                            <span>PaddingY</span>
                        </label>
                    </b-colxx>
                </b-row>
            </div>

            <button class="btn mt-4 btn-primary" @click="setHead">Save</button>
        </b-tab>
    </b-tabs>

    <b-modal
        id="add_modal"
        ref="add_modal"
        :title="titleText"
        modal-class="modal-center"
    >
        <b-form>
            <b-form-group label="Title">
                <b-form-input v-model="menuTitle" />
            </b-form-group>
        </b-form>
        <template slot="modal-footer">
            <b-button
                variant="outline-secondary"
                @click="hideModal()"
            >{{ $t('cancel') }}</b-button>
            <b-button
                variant="primary"
                @click="addNewItem(menuTitle)"
                class="mr-1"
            >
                {{submitText}}
            </b-button>
        </template>
    </b-modal>

    <b-modal id="view_modal" size="lg" :title="viewTitle" centered hide-footer>
        <video v-if="extension === 'mp4'" width="100%" height="auto" autoplay="autoplay" loop="loop" muted="">
            <source :src="'/data/'+viewItem" type="video/mp4">
        </video>

        <iframe class="w-100" :src="'/data/'+viewItem" v-else-if="extension === 'pdf'" style="height: 75vh" />

        <iframe class="w-100" :src="viewItem" v-else-if="viewItem.substring(0, 4) === 'http'" style="height: 75vh" />
        
        <img class="w-100" :src="'/data/'+viewItem" v-else />
    </b-modal>

    <b-modal id="confirm_modal" ref="confirm_modal" title="Are you sure?">
        If you click confirm, you can't recover this data anymore!
        <template slot="modal-footer">
            <b-button variant="primary" @click="confirm()" class="mr-1">Confirm</b-button>
            <b-button variant="secondary" @click="hideModal()">Cancel</b-button>
        </template>
    </b-modal>

    <b-modal
        id="asset_modal"
        ref="asset_modal"
        :title="assetTitle"
        modal-class="modal-center"
    >
        <b-form>
            <b-form-group label="Title">
                <b-form-input v-model="assetItem.title" />
            </b-form-group>
            <b-form-group label="Type">
                <v-select v-model="assetType" :options="types" />
            </b-form-group>
            <b-form-radio-group v-model="videoOption" v-if="assetType.value">
                <b-form-radio :value="0">Upload</b-form-radio>
                <b-form-radio :value="1">Link</b-form-radio>
            </b-form-radio-group>
            <b-form-group class="my-2" v-if="videoOption && assetType.value">
                <b-form-input v-model="assetItem.file" />
            </b-form-group>
            <b-form-group class="my-2" v-else>
                <b-input-group :prepend="$t('input-groups.upload')">
                    <b-form-file ref="file" :placeholder="$t('input-groups.choose-file')" @change="onAssetFileUpload" />
                </b-input-group>
            </b-form-group>
        </b-form> 

        <template slot="modal-footer">
            <b-button
                variant="outline-secondary"
                @click="hideModal('add_modal')"
            >{{ $t('cancel') }}</b-button>
            <b-button variant="primary" @click="addNewAssetItem(assetItem)" class="mr-1">{{ $t('submit') }}</b-button>
        </template>
    </b-modal>

    <input id="media" type="file" @change="onFileChange" class="d-none" onclick="this.value = null" />
</div>
</template>

<script>
import { mapGetters } from 'vuex'
import Axios from "axios"
import Draggable from 'vuedraggable'
import vSelect from "vue-select"
import "vue-select/dist/vue-select.css"

import PosterType from "../../../../containers/Poster/PosterType"
import Header from "../../../../containers/Web/DomainHeader"
import AddAssetModal from '../../../../components/Wizard/AddAssetModal'
import QuillEditor from '../../../../components/Pages/QuillEditor'
import StatusButton from '../../../../components/Pages/StatusButton'

export default {
    components: {
        'poster-type': PosterType,
        'b-header': Header,
        'draggable': Draggable,
        'b-asset-modal': AddAssetModal,
        "b-quill-editor" : QuillEditor,
        "v-select": vSelect,
        'b-status-button': StatusButton
    },
    metaInfo () {
        return { title: `Poster Edit` }
    },
    data() {
        return {
            id: null,
            viewType: null,
            posterItem: {},
            items: [],
            status: ['Active', 'InActive', 'Delete'],
            apiBase: '/wizard/poster/',
            extension: '',
            header: [],
            menus: [],
            selectedMenus: [],
            headerStyle: {
                bg: '#922c88',
                height: 32,
                btn: {
                    bg: '#d683ce',
                    color: '#ffffff',
                    radius: 4,
                    space: 8,
                    boxShadow: true,
                    hasBg: true,
                    fontSize: 13,
                    paddingX: 2,
                    paddingY: 8
                }
            },
            type: 'type',
            url: null,
            formData: new FormData(),
            changeUrl: null,
            assets: [],
            selectedAssets: [],

            menuTitle: '',
            editId: 0,
            submitText: 'Create',
            titleText: 'Create Menu',
            successText: 'Successfully Created!',
            isDelete: false,
            currentStatus: 0,

            editData: {},
            viewItem: '',
            viewTitle: '',

            editAssetData: {},

            submitText: 'Create Page',
            setting: 'menus',
            editorId: null,
            categories: [],

            url_0: null, url_1: null, url_2: null, url_3: null, url_4: null, url_5: null, url_6: null, url_7: null, url_8: null, url_9: null, url_10: null, url_11: null, url_12: null, url_13: null, url_14: null, url_15: null, url_16: null, url_17: null, url_18: null, url_19: null, url_20: null,
            changeUrl_0: null, changeUrl_1: null, changeUrl_2: null, changeUrl_3: null, changeUrl_4: null, changeUrl_5: null, changeUrl_6: null, changeUrl_7: null, changeUrl_8: null, changeUrl_9: null, changeUrl_10: null, changeUrl_11: null, changeUrl_12: null,           changeUrl_13: null, changeUrl_14: null, changeUrl_15: null, changeUrl_16: null, changeUrl_17: null, changeUrl_18: null, changeUrl_19: null, changeUrl_20: null,
            extension_0: '', extension_1: '', extension_2: '', extension_3: '', extension_4: '', extension_5: '', extension_6: '', extension_7: '', extension_8: '', extension_9: '', extension_10: '', extension_11: '', extension_12: '', extension_13: '', extension_14: '', extension_15: '', extension_16: '', extension_17: '', extension_18: '', extension_19: '', extension_20: '',
            currentIndex: null,

            titleEdit: false,

            tabIndex: 0,

            posterHeader: {
                position: {
                    x: 1, y: 1, ex: 28, ey: 10
                },
                bg: '#ffffff',
                text: {
                    color: '#000000',
                    size: 28
                }
            },
            posterContent: [],
            posterContent1: [],
            layerStyle: {
                title: '',
                text: '',
                position: {
                    x: 1, y: 11, ex: 33, ey: 99
                },
                bg: '#ffffff',
                padding: 8,
                zIndex: 0,
                type: 'text',
                borderRadius: 8,
                header: {
                    color: '#000000',
                    size: 24
                },
                body: {
                    color: '#000000',
                    size: 16,
                    bg: '#ffffff',
                    borderRadius: 8,
                    padding: 8,
                    position: {
                        x: 0, y: 0, ex: 100, ey: 100
                    }
                },
                media: {
                    borderRadius: 8,
                    padding: 8,
                    bg: '#ffffff',
                    position: {
                        x: 0, y: 0, ex: 100, ey: 100
                    }
                }
            },
            contentTypes: ['text', 'media'],

            assetTitle: 'Add new Asset',
            assetItem: {
                id: 0,
                title: '',
                file: null
            },
            tabModel: 0,
            tabMenu: 0,
            tabs: [],
            isDelStatus: 0,
            isConfirmed: false,
            types: [
                { label: 'Image or PDF', value: 0 },
                { label: 'Video', value: 1 }
            ],
            assetType: '',
            videoOption: 0,
            contentIndex: 0
        }
    },
    computed: {
        ...mapGetters({
            siteId: 'site/getSiteId',
            user: 'auth/user'
        }),
        isSelectedAll() {
            if (this.type == 'modal') {
                return this.selectedMenus.length >= this.menus.length;
            } else if (this.type == 'content') {
                if (this.assets) {
                    return this.selectedAssets.length >= this.assets.length
                } else {
                    return true
                }
            }
        },
        isAnyItemSelected() {
            if (this.type == 'modal') {
                return (
                    this.selectedMenus.length > 0 &&
                    this.selectedMenus.length < this.menus.length
                )
            } else if (this.type == 'content') {
                return (
                    this.selectedAssets.length > 0 &&
                    this.selectedAssets.length < this.assets.length
                )
            }
        },
        length() {
            return 3-this.items.length
        }
    },
    methods: {
        loadItems() {
            Axios.post(this.apiBase + 'get', {id: this.id}).then(res => {
                if (res.statusText == 'OK') {
                    this.posterItem = res.data.posterItem
                    this.viewType = this.posterItem.type
                    if (this.viewType) {
                        this.tabIndex = 1
                    }
                    if (this.viewType == 3 && this.posterItem.layers) {
                        this.posterContent = JSON.parse(this.posterItem.layers)
                    } else if (this.viewType == 2 && this.posterItem.media) {
                        this.items = JSON.parse(this.posterItem.media)
                    } else if (this.viewType == 1 && this.posterItem.media) {
                        this.items.push(this.posterItem.media)
                    }
                    if (this.posterItem.posterHeader) {
                        this.posterHeader = JSON.parse(this.posterItem.posterHeader)
                    }
                    this.menus = res.data.resource
                    this.header = this.posterItem.header

                    if (this.posterItem.header_style) {
                        this.headerStyle = JSON.parse(this.posterItem.header_style)
                    }
                }
            })
        },
        getHeader() {
            let headerArray = []
            let sourceArray = []
            this.header.forEach(menu => {
                headerArray.push(menu.id)
            })
            this.menus.forEach(menu => {
                sourceArray.push(menu.id)
            })
            Axios.post(this.apiBase + 'order', {header: headerArray, source: sourceArray, id: this.id}).then(res => {
                if (res.status == 200) {
                    this.header = res.data.header
                }
            })
        },
        addNewItem(item) {
            if (item == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert all fields', { duration: 3000, permanent: false })
            } else {
                Axios.post(this.apiBase + 'setHeader', {title: item, id: this.id, editId: this.editId}).then(res => {
                    if (res.statusText == 'OK' && res.data.resource && res.data.item.header) {
                        this.$notify('primary filled', '', this.successText, { duration: 3000, permanent: false })
                        this.menus = res.data.resource
                        this.header = res.data.item.header
                        this.selectedMenus = []
                        this.successText = 'Successfully Created!'
                    } else {
                        this.$notify('primary filled', 'Sorry!', 'Please fill the correct Data!', { duration: 3000, permanent: false })
                    }
                })
                this.$refs['add_modal'].hide()
            }
        },
        addNewAssetItem(item) {
            let onAdd = true
            if (item.title == '') {
                this.$notify('primary filled', 'Warn!', 'Please insert file title', { duration: 3000, permanent: false })
                onAdd = false            }
            if (!item.file) {
                this.$notify('primary filled', 'Warn!', 'Please insert file', { duration: 3000, permanent: false })
                onAdd = false
            }
            if (onAdd) {
                var id = this.tabs[this.tabMenu][this.tabModel].id
                var formData = new FormData()
                formData.append('title', item.title)
                formData.append('file', item.file)
                formData.append('id', id)
                formData.append('boothId', this.id)
                formData.append('editId', this.editId)
                Axios.post(this.apiBase + 'add-asset', formData).then(res => {
                    if (res.statusText == 'OK') {
                        this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                        this.header = res.data.header
                        this.hideModal()
                        this.assetItem = {
                            id: 0,
                            title: '',
                            file: null
                        }
                    }
                })
            }
        },
        setStatus(value) {
            this.currentStatus = value
            if (value == 2 && !this.isDelete) {
                this.$refs['confirm_modal'].show()
            } else {
                if (this.type == 'modal') {
                    Axios.put(this.apiBase + 'menuStatus', {status: value, items: this.selectedMenus, id: this.id}).then(res => {
                        if (res.statusText == 'OK') {
                            this.$notify('primary filled', '', this.notifyText, { duration: 3000, permanent: false })
                            this.menus = res.data.resource
                            this.header = res.data.item.header
                            this.selectedMenus = []
                        } else {
                            this.$notify('primary filled', 'Ooop!', 'Something went wrong!', { duration: 3000, permanent: false })
                        }
                    })
                } else {
                    Axios.post(this.apiBase + 'mediaStatus', {status: value, items: this.selectedAssets, id: this.id}).then(res => {
                        if (res.status == 200) {
                            if (status == 2) {
                                this.$notify('primary filled', '', 'Successfully Deleted!', { duration: 3000, permanent: false })
                            } else {
                                this.$notify('primary filled', '', 'Successfully Changed!', { duration: 3000, permanent: false })
                            }
                            this.header = res.data.header
                            this.selectedAssets = []
                        } else {
                            this.$notify('primary filled', '', 'Server Error! Please try a few hours later!', { duration: 3000, permanent: false })
                        }
                    })
                }
                this.isDelete = false
                this.$refs['confirm_modal'].hide()
                this.notifyText = 'Successfully Changed!'
            }
        },
        setHead() {
            Axios.put(this.apiBase + 'head', {style: this.headerStyle, id: this.id}).then(res => {
                if (res.statusText == 'OK') {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Sorry!', 'Something went wrong!', { duration: 3000, permanent: false })
                }
            })
        },

        view(item) {
            this.viewItem = item.item
            this.viewTitle = item.title
            this.extension = this.viewItem.split('.').pop()
        },
        edit(item) {
            if (this.type == 'content') {
                this.assetItem = item
            } else {
                this.editData = item
            }
        },
        hideModal() {
            this.$refs['add_modal'].hide()
            this.$refs['confirm_modal'].hide()
            this.$refs['asset_modal'].hide()
        },
        confirm() {
            this.isDelete = true
            this.notifyText = 'Successfully Deleted!'
            if (this.isDelStatus == 2) {
                this.closeTab()
            } else {
                this.setStatus(this.currentStatus)
            }
        },
        initData() {
            if (this.type == 'content') {
                this.assetItem = {}
            } else {
                this.editData = {}
            }
        },
        capitalizeFirstLetter(string) {
            return string.charAt(0).toUpperCase() + string.slice(1)
        },

        upload() {
            this.formData.append('id', this.posterItem.id)
            this.formData.append('title', this.posterItem.title)
            Axios.post(this.apiBase + 'upload', this.formData).then(res => {
                if(res.statusText == 'OK' && res.data) {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                    this.items[0] = res.data.media
                    this.changeUrl = null
                } else {
                    this.$notify('primary filled', 'Ooop!', 'Something went wrong!', { duration: 3000, permanent: false })
                }
            })
        },
        multipleUpload() {
            this.formData.append('id', this.posterItem.id)
            this.formData.append('title', this.posterItem.title)
            Axios.post(this.apiBase + 'multiple-upload', this.formData).then(res => {
                if(res.statusText == 'OK') {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Ooop!', 'Something went wrong!', { duration: 3000, permanent: false })
                }
            })
        },
        advancedUpload() {
            this.formData.append('id', this.posterItem.id)
            this.formData.append('title', this.posterItem.title)
            this.formData.append('layers', JSON.stringify(this.posterContent))
            this.formData.append('posterHeader', JSON.stringify(this.posterHeader))
            Axios.post(this.apiBase + 'advancedUpload', this.formData).then(res => {
                if(res.statusText == 'OK') {
                    this.$notify('primary filled', '', 'Successfully Saved!', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Ooop!', 'Something went wrong!', { duration: 3000, permanent: false })
                }
            })
        },
        onFileChange(e) {
            const file = e.target.files[0]
            this.extension = file.name.split('.').pop()
            if (this.extension != 'mp4') {
                if (file.size / 10000 > 5) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 500kb.', { duration: 3000, permanent: false })
                }
            } else {
                if (file.size / 100000 > 1) {
                    this.$notify('primary filled', 'File is not optimized for the sytem', 'Please reduce reupload with a size of less than 1mb.', { duration: 3000, permanent: false })
                }
            }

            if (this.viewType == 1) {
                if (this.items.length) {
                    this.changeUrl = URL.createObjectURL(file)
                } else {
                    this.url = URL.createObjectURL(file)
                }
                this.formData.set('file', file)
            } else {
                this['extension_' + this.currentIndex] = this.extension
                if (this.items.length > this.currentIndex) {
                    this['changeUrl_' + this.currentIndex] = URL.createObjectURL(file)
                } else {
                    this['url_' + this.currentIndex] = URL.createObjectURL(file)
                }
                this.formData.append('file' + this.currentIndex, file)
            }
        },
        getBackdrop(data) {
            if (data && data.category && data.category.backdrop) {
                return {
                    'background': 'url(/assets/img/poster-backdrop/' + data.category.backdrop.media + ')',
                    'background-position': 'bottom'
                }
            }
        },
        getBackdropType(data) {
            if (data && data.category && data.category.backdrop) {
                return {
                    'background': 'url(/assets/img/poster-backdrop/' + data.category.backdrop.media + ')',
                    'width': '65%',
                    'height': '65%',
                    'background-position': 'bottom'
                }
            }
        },
        getType(file) {
            const extension = file.split('.').pop()
            if (extension == 'mp4') {
                return '/assets/img/mp4.png'
            } else if (extension == 'pdf') {
                return '/assets/img/pdf.png'
            } else {
                return '/assets/img/pic.png'
            }
        },

        keymap(event) {
            switch (event.srcKey) {
                case "select":
                    this.selectAll(false);
                break;
                case "undo":
                    if (this.type == 'modal') {
                        this.selectedMenus = [];
                    } else {
                        this.selectedAssets = [];
                    }
                break;
            }
        },
        toggleItem(event, itemId) {
            if (event.shiftKey && this.selectedMenus.length > 0) {
                if (this.type == 'modal') {
                    let itemsForToggle = this.menus;
                    var start = this.getIndex(itemId, itemsForToggle, "id");
                    var end = this.getIndex(
                        this.selectedMenus[this.selectedMenus.length - 1],
                        itemsForToggle,
                        "id"
                    );
                    itemsForToggle = itemsForToggle.slice(
                        Math.min(start, end),
                        Math.max(start, end) + 1
                    );
                    this.selectedMenus.push(
                        ...itemsForToggle.map(item => {
                            return item.id;
                        })
                    );
                } else {
                    let itemsForToggle = this.assets;
                    var start = this.getIndex(itemId, itemsForToggle, "id");
                    var end = this.getIndex(
                        this.selectedAssets[this.selectedAssets.length - 1],
                        itemsForToggle,
                        "id"
                    );
                    itemsForToggle = itemsForToggle.slice(
                        Math.min(start, end),
                        Math.max(start, end) + 1
                    );
                    this.selectedAssets.push(
                        ...itemsForToggle.map(item => {
                            return item.id;
                        })
                    );
                }
            } else {
                if (this.type == 'modal') {
                    if (this.selectedMenus.includes(itemId)) {
                        this.selectedMenus = this.selectedMenus.filter(x => x !== itemId);
                    } else this.selectedMenus.push(itemId);
                } else {
                    if (this.selectedAssets.includes(itemId)) {
                        this.selectedAssets = this.selectedAssets.filter(x => x !== itemId);
                    } else this.selectedAssets.push(itemId);
                }
            }
        },
        selectAll(isToggle) {
            if (this.type == 'modal') {
                if (this.selectedMenus.length >= this.menus.length) {
                    if (isToggle) this.selectedMenus = [];
                } else {
                    this.selectedMenus = this.menus.map(x => x.id);
                }
            } else {
                if (this.selectedAssets.length >= this.assets.length) {
                    if (isToggle) this.selectedAssets = [];
                } else {
                    this.selectedAssets = this.assets.map(x => x.id);
                }
            }
        },
        onEditorChange({ editor, html, text }) {
            this.tabs[this.tabMenu][this.contentIndex].content = html
        },

        getHeaderStyle() {
            return {
                'background-color': this.headerStyle.bg,
                'height': this.headerStyle.height + 'px'
            }
        },
        getButtonStyle() {
            let boxShadow = '0px 3px 1px -2px rgba(0,0,0,0.2), 0px 2px 2px 0px rgba(0,0,0,0.14), 0px 1px 5px 0px rgba(0,0,0,0.12)'
            let bg = this.headerStyle.btn.bg
            if (!this.headerStyle.btn.boxShadow) {
                boxShadow = 'none'
            }
            if (!this.headerStyle.btn.hasBg) {
                bg = 'transparent'
            }
            const buttonHeight = 2 * Number(this.headerStyle.btn.paddingX) +Number( this.headerStyle.btn.fontSize)
            return {
                'background-color': bg,
                'color': this.headerStyle.btn.color,
                'margin-left': this.headerStyle.btn.space + 'px',
                'margin-right': this.headerStyle.btn.space + 'px',
                'border-radius': this.headerStyle.btn.radius + 'px',
                'margin-top': ((this.headerStyle.height - buttonHeight) / 2 - 5) + 'px',
                'font-size': this.headerStyle.btn.fontSize + 'px',
                'box-shadow': boxShadow,
                'padding-top': this.headerStyle.btn.paddingX + 'px',
                'padding-bottom': this.headerStyle.btn.paddingX + 'px',
                'padding-left': this.headerStyle.btn.paddingY + 'px',
                'padding-right': this.headerStyle.btn.paddingY + 'px'
            }
        },

        addTab(index) {
            this.tabs[index].push(
                {title: 'Tab'}
            )
            var tabs = this.tabs
            this.tabs = []
            this.tabs = tabs
            this.setContent() 
        },
        closeTab() {
            if (this.isDelStatus == 0 && !this.isDelete) {
                this.isDelStatus = 2
                this.$refs['confirm_modal'].show()
            } else {
                var id = this.tabs[this.tabMenu][this.tabModel].id
                Axios.post(this.apiBase + 'delTab', {delId: id, id: this.id}).then(res => {
                   this.header = res.data.header
                })
                this.isDelStatus = 0
                this.isDelete = false
                this.$refs['confirm_modal'].hide()
            }
        },
        updateTitle(title) {
            if (title == '') {
                this.$notify('primary filled', '', 'Please Insert Title!', { duration: 3000, permanent: false })
            } else {
                var tabs = this.tabs
                this.tabs = []
                this.tabs = tabs
                this.setContent()
            }
        },
        getTab(id) {
            this.editorId = id;
        },
        typeImg (media) {
            const extension = media.split('.').pop()
            if (extension != 'pdf' && extension != 'mp4') {
                return true
            } else {
                return false
            }
        },
        typeVideo (media) {
            const extension = media.split('.').pop()
            if (extension == 'mp4') {
                return true
            } else {
                return false
            }
        },
        typePdf (media) {
            const extension = media.split('.').pop()
            if (extension == 'pdf') {
                return true
            } else {
                return false
            }
        },

        goNext() {
            if (this.viewType) {
                this.tabIndex = 1
            } else {
                this.$notify('primary filled', '', 'Please Choose the Type', { duration: 3000, permanent: false })
            }
        },
        setType(val) {
            this.viewType = val
        },
        fileType (media) {
            return media.split('.').pop()
        },
        isChanged(index) {
            if (this['changeUrl_' + index]) {
                return true
            } else {
                return false
            }
        },
        isUploaded(index) {
            if (this['url_' + index]) {
                return true
            } else {
                return false
            }
        },
        getChangeUrl(index) {
            return this['changeUrl_' + index]
        },
        getUrl(index) {
            return this['url_' + index]
        },
        getExtension(index) {
            return this['extension_' + index]
        },

        getPosterHeaderStyle(data) {
            return {
                'background': data.bg,
                'color': data.text.color,
                'font-size': data.text.size + 'px',
                'text-align': 'center',
                'left': Math.min(data.position.x, data.position.ex) + '%',
                'top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%'
            }
        },
        contentStyle(data) {
            return {
                'padding': data.padding + 'px',
                'background': data.bg,
                'z-index': data.zIndex,
                'left': Math.min(data.position.x, data.position.ex) + '%',
                'top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'border-radius': data.borderRadius + 'px'
            }
        },
        contentHeaderStyle(data) {
            return {
                'color': data.color,
                'font-size': data.size + 'px'
            }
        },
        contentBodyStyle(data) {
            return {
                'color': data.color,
                'font-size': data.size + 'px',
                'margin-left': Math.min(data.position.x, data.position.ex) + '%',
                'margin-top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'word-break': 'break-all',
                'background': data.bg,
                'border-radius': data.borderRadius + 'px',
                'padding': data.padding + 'px',
            }
        },
        contentMediaStyle(data) {
            return {
                'margin-left': Math.min(data.position.x, data.position.ex) + '%',
                'margin-top': Math.min(data.position.y, data.position.ey) + '%',
                'width': Math.abs(data.position.ex - data.position.x) + '%',
                'height': Math.abs(data.position.ey - data.position.y) + '%',
                'border-radius': data.borderRadius + 'px',
                'padding': data.padding + 'px',
                'background': data.bg
            }
        },
        del(index) {
            this.posterContent.splice(index, 1)
        },
        addLayer() {
            const layerStyle = {
                title: '',
                text: '',
                position: {
                    x: 1, y: 11, ex: 33, ey: 99
                },
                bg: '#ffffff',
                padding: 8,
                zIndex: 0,
                type: 'text',
                borderRadius: 8,
                header: {
                    color: '#000000',
                    size: 24
                },
                body: {
                    color: '#000000',
                    size: 16,
                    bg: '#ffffff',
                    borderRadius: 8,
                    padding: 8,
                    position: {
                        x: 0, y: 0, ex: 100, ey: 100
                    }
                },
                media: {
                    borderRadius: 8,
                    padding: 8,
                    bg: '#ffffff',
                    position: {
                        x: 0, y: 0, ex: 100, ey: 100
                    },
                    file: ''
                }
            }
            this.posterContent.push(layerStyle)
        },
        returnTab() {
            if (this.tabIndex != 0) {
                this.tabIndex--
            }
        },
        nextTab() {
            if (this.tabIndex != 4) {
                this.tabIndex++
            }
        },
        getTab(id, index) {
            this.editorId = id;
            this.tabMenu = index
        },
        onAssetFileUpload(e) {
            this.assetItem.file = e.target.files[0] 
        },
        setContent() {
            Axios.post(this.apiBase + 'content', {data: this.header, tabs: this.tabs, id: this.id}).then(res => {
                this.header = res.data.header
            })
        },
        publish() {
            Axios.post(this.apiBase + 'set-status', {id: this.id, status: 2, siteId: this.siteId}).then(res => {
                if (res.status == 200) {
                    this.$notify('primary filled', '', 'You have successfully completed your poster, our team will review.  Come back to edit your poster or check out the event.', { duration: 3000, permanent: false })
                } else {
                    this.$notify('primary filled', 'Server Error!', 'Please try a few hours later!', { duration: 3000, permanent: false })
                }
            })
        }
    },
    watch: {
        header(val) {
            if (val.length) {
                this.editorId = val[0].id
            }
            this.tabs = []
            val.forEach((element, index) => {
                this.tabs[index] = element.tab
                if (this.tabs[index].length == 0) {
                    this.tabs[index].push({
                        title: 'Resource'
                    })
                    this.tabs[index].push({
                        title: 'Video'
                    })
                    this.setContent()
                }
            });

            if (val.length && val[this.tabMenu] && val[this.tabMenu].tab && val[this.tabMenu].tab[this.tabModel]) {
                this.assets = this.header[this.tabMenu].tab[this.tabModel].media
            } else {
                this.assets = []
            }
        },
        videoOption(val) {
            if (val) {
                this.assetItem.file = null
            } else {
                this.assetItem.file = ''
            }
        },
        editData: function(val) {
            if (val.id) {
                this.titleText = "Edit The Title"
                this.menuTitle = val.title
                this.submitText = 'Change'
                this.editId = val.id
            } else {
                this.titleText = "Create Menu"
                this.menuTitle = ''
                this.submitText = 'Create'
                this.editId = 0
            }
        },
        tabIndex(val) {
            if (val == 0) {
                this.type = 'type'
            } else if (val == 4) {
                this.type = 'settings'
            } else if (val == 1) {
                this.type = 'upload'
            } else if (val == 2 ) {
                this.type = 'modal'
            } else {
                this.type = 'content'
            }
        },
        assetItem(val) {
            if (val.id) {
                this.titleText = "Edit The Asset"
                this.menuTitle = val.title
                this.submitText = 'Change'
                this.editId = val.id
                this.notifyText = 'Successfully Changed!'
            } else {
                this.titleText = "Create Menu"
                this.menuTitle = ''
                this.submitText = 'Create'
                this.editId = 0
                this.notifyText = 'Successfully Created!'
            }
        },
        tabMenu(val) {
            if (this.header[val] && this.header[val].tab && this.header[val].tab[this.tabModel]) {
                this.assets = this.header[val].tab[this.tabModel].media
            } else {
                this.assets = []
            }
        },
        tabModel(val) {
            if (this.header[this.tabMenu] && this.header[this.tabMenu].tab && this.header[this.tabMenu].tab[val]) {
                this.assets = this.header[this.tabMenu].tab[val].media
            } else {
                this.assets = []
            }
        }
    },
    mounted() {
        this.id = this.$route.params.id
        this.loadItems()
    }
}
</script>