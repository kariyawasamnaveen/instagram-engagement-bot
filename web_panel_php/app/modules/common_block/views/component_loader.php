
<style>
.main-loader {
    background-color: #ffffff;
    position: relative;
    border-radius: 10px;
    height: <?= $height; ?>;
}

.main-loader .circle-loader {
  position: absolute;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
}

.circle-loader {
  width: 30px;
  height: 30px;
  border: 5px dotted #e0e3f5;
  border-radius: 50%;
  display: inline-block;
  position: relative;
  box-sizing: border-box;
  animation: rotation 2s linear infinite;
}

@keyframes rotation {
  0% {
    transform: rotate(0deg);
  }
  100% {
    transform: rotate(360deg);
  }
} 
</style>

<div class="main-loader">
    <span class="circle-loader"></span>
</div>