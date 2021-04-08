const canvas = document.getElementById("renderCanvas"); // Get the canvas element
const engine = new BABYLON.Engine(canvas, true); // Generate the BABYLON 3D engine
const filePath = document.getElementById("model3d").innerHTML;
const fileName = filePath.substring(filePath.lastIndexOf("/") + 1);
//***PG */

//Scene and camera
var createScene = function () {
  var scene = new BABYLON.Scene(engine);
  var camera = new BABYLON.ArcRotateCamera(
    "Camera",
    Math.PI / 3,
    Math.PI / 2,
    3.5,
    new BABYLON.Vector3(0, 0.5, 0),
    scene
  );
  camera.attachControl(canvas, false);
  camera.minZ = 0.1;
  camera.wheelDeltaPercentage = 0.01;
  camera.upperRadiusLimit = 10;
  camera.lowerRadiusLimit = 2;
  camera._panningMouseButton = null;

  // Create a 'sphere' to use as camera target
  var sphere = BABYLON.MeshBuilder.CreateSphere(
    "sphere",
    { diameter: 0.01, segments: 4 },
    scene
  );
  // Move the sphere upward
  sphere.position.y = 1;
  //Set camera target
  camera.target = sphere.absolutePosition;

  /**
   * ASYNC/AWAIT Function to load a model into the scene
   * @param {*} meshNames | can be "" for any
   * @param {*} rootUrl
   * @param {*} fileName
   */
  async function loadMeshes(meshNames, rootUrl, fileName) {
    var model = await BABYLON.SceneLoader.ImportMeshAsync(
      meshNames,
      rootUrl,
      fileName
    );
    //Add shadow caster to each mesh within model
    model.meshes.forEach((element) =>
      shadowGenerator.addShadowCaster(element, true)
    );

  }

  loadMeshes("", "/assets/models/", fileName);

  //Setup environment
  var env = scene.createDefaultEnvironment({
    createSkybox: true,
    skyboxSize: 150,
    skyboxColor: new BABYLON.Color3(0.0375,0.0375,0.0375),
    createGround: true,
    groundSize: 0,
    groundColor: new BABYLON.Color3(0.037,0.037,0.037),
    enableGroundShadow: true,
    groundYBias: 1,
  });

  //Lights
  var dLight = new BABYLON.DirectionalLight(
    "dLight",
    new BABYLON.Vector3(0.02, -0.05, -0.05),
    scene
  );
  dLight.position = new BABYLON.Vector3(0, 20, 0);
  var pLight = new BABYLON.PointLight(
    "pLight",
    new BABYLON.Vector3(5, 10, -5),
    scene
  );
  pLight.diffuse = new BABYLON.Color3(0.53, 0.66, 0.74);
  pLight.specular = new BABYLON.Color3(0.83, 0.86, 0.89);

  //Shadows
  var shadowGenerator = new BABYLON.ShadowGenerator(2048, dLight);
  shadowGenerator.useBlurExponentialShadowMap = true;

  // Code in this function will run ~60 times per second
  scene.registerBeforeRender(function () {
    //Slowly rotate camera
    camera.alpha += 0.002;
  });

  return scene;
};

//***/PG */

const scene = createScene(); //Call the createScene function

// Register a render loop to repeatedly render the scene
engine.runRenderLoop(function () {
  scene.render();
});

// Watch for browser/canvas resize events
window.addEventListener("resize", function () {
  engine.resize();
});
