<template>
  <div class="ball-container">
    <h1 class="title">Hello World</h1>
    <div class="octagon" :style="{ transform: 'rotate(' + angle + 'deg)' }"></div>
    <div class="ball" :style="{ top: ballY + 'px', left: ballX + 'px' }"></div>
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from 'vue'

const angle = ref(0)
const ballX = ref(390)
const ballY = ref(290)  
const velocityX = ref(3)
const velocityY = ref(2)
const ballRadius = 10
const octagonSize = 100 // Half the octagon width/height
const centerX = 400
const centerY = 300
const gravity = 0.15
const bounce = 0.95
const friction = 0.995
const rotationSpeed = 1
const wallEnergyTransfer = 0.3

let animationId: number

// Define octagon vertices (relative to center)
const getOctagonVertices = (rotation: number) => {
  const vertices = []
  for (let i = 0; i < 8; i++) {
    const angleRad = (i * 45 + rotation) * Math.PI / 180
    const x = centerX + octagonSize * Math.cos(angleRad)
    const y = centerY + octagonSize * Math.sin(angleRad)
    vertices.push({ x, y })
  }
  return vertices
}

// Check if point is inside octagon using ray casting
const isInsideOctagon = (x: number, y: number, rotation: number) => {
  const vertices = getOctagonVertices(rotation)
  let inside = false
  
  for (let i = 0, j = vertices.length - 1; i < vertices.length; j = i++) {
    if (((vertices[i].y > y) !== (vertices[j].y > y)) &&
        (x < (vertices[j].x - vertices[i].x) * (y - vertices[i].y) / (vertices[j].y - vertices[i].y) + vertices[i].x)) {
      inside = !inside
    }
  }
  return inside
}

// Find closest point on octagon edge and distance
const getClosestEdgePoint = (ballCenterX: number, ballCenterY: number, rotation: number) => {
  const vertices = getOctagonVertices(rotation)
  let minDist = Infinity
  let closestPoint = { x: 0, y: 0 }
  let normalX = 0
  let normalY = 0
  
  for (let i = 0; i < vertices.length; i++) {
    const v1 = vertices[i]
    const v2 = vertices[(i + 1) % vertices.length]
    
    // Calculate closest point on edge
    const edgeX = v2.x - v1.x
    const edgeY = v2.y - v1.y
    const edgeLength = Math.sqrt(edgeX * edgeX + edgeY * edgeY)
    const edgeNormX = edgeX / edgeLength
    const edgeNormY = edgeY / edgeLength
    
    const toBallX = ballCenterX - v1.x
    const toBallY = ballCenterY - v1.y
    const projection = Math.max(0, Math.min(edgeLength, toBallX * edgeNormX + toBallY * edgeNormY))
    
    const pointX = v1.x + projection * edgeNormX
    const pointY = v1.y + projection * edgeNormY
    
    const distX = ballCenterX - pointX
    const distY = ballCenterY - pointY
    const dist = Math.sqrt(distX * distX + distY * distY)
    
    if (dist < minDist) {
      minDist = dist
      closestPoint = { x: pointX, y: pointY }
      // Normal points inward toward ball
      normalX = distX / dist
      normalY = distY / dist
    }
  }
  
  return { distance: minDist, point: closestPoint, normalX, normalY }
}

const animate = () => {
  // Update octagon rotation
  angle.value += rotationSpeed
  if (angle.value > 360) {
    angle.value = 0
  }

  // Apply gravity
  velocityY.value += gravity

  // Apply friction
  velocityX.value *= friction
  velocityY.value *= friction

  // Update ball position
  ballX.value += velocityX.value
  ballY.value += velocityY.value

  const ballCenterX = ballX.value + ballRadius
  const ballCenterY = ballY.value + ballRadius

  // Check collision with octagon edges
  const collision = getClosestEdgePoint(ballCenterX, ballCenterY, angle.value)
  
  if (collision.distance <= ballRadius) {
    // Calculate tangent vector for wall velocity
    const tangentX = -collision.normalY
    const tangentY = collision.normalX

    // Wall velocity from rotation
    const angularVelocity = rotationSpeed * Math.PI / 180
    const wallVelocityX = tangentX * octagonSize * angularVelocity
    const wallVelocityY = tangentY * octagonSize * angularVelocity

    // Reflect velocity
    const dot = velocityX.value * collision.normalX + velocityY.value * collision.normalY
    velocityX.value -= 2 * dot * collision.normalX
    velocityY.value -= 2 * dot * collision.normalY
    
    // Add energy from rotating wall
    velocityX.value += wallVelocityX * wallEnergyTransfer
    velocityY.value += wallVelocityY * wallEnergyTransfer

    // Apply bounce
    velocityX.value *= bounce
    velocityY.value *= bounce

    // Ensure minimum velocity
    const speed = Math.sqrt(velocityX.value * velocityX.value + velocityY.value * velocityY.value)
    if (speed < 1.5) {
      const minSpeed = 2
      velocityX.value = (velocityX.value / speed) * minSpeed
      velocityY.value = (velocityY.value / speed) * minSpeed
    }

    // Push ball out of collision
    const overlap = ballRadius - collision.distance
    ballX.value += overlap * collision.normalX
    ballY.value += overlap * collision.normalY
  }

  animationId = requestAnimationFrame(animate)
}

onMounted(() => {
  animate()
})

onUnmounted(() => {
  if (animationId) {
    cancelAnimationFrame(animationId)
  }
})
</script>

<style scoped>
.title {
  position: absolute;
  top: 2rem;
  left: 50%;
  transform: translateX(-50%);
  font-size: 3rem;
  font-weight: bold;
  color: white;
  z-index: 10;
}

.ball-container {
  position: relative;
  width: 800px;
  height: 600px;
  margin: 0 auto;
  background: linear-gradient(135deg, #1e3a8a, #7c3aed);
}

.octagon {
  position: absolute;
  top: 50%;
  left: 50%;
  transform-origin: center;
  width: 200px;
  height: 200px;
  background-color: #333;
  clip-path: polygon(30% 0%, 70% 0%, 100% 30%, 100% 70%, 70% 100%, 30% 100%, 0% 70%, 0% 30%);
  margin: -100px 0 0 -100px;
  animation: rotate 4s linear infinite;
}

.ball {
  position: absolute;
  border-radius: 50%;
  background-color: #fff;
  width: 20px;
  height: 20px;
  box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}

@keyframes rotate {
  from {
    transform: rotate(0deg);
  }
  to {
    transform: rotate(360deg);
  }
}
</style>