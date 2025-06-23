# Layout Tree構造の説明

## 概要

Layout Treeは、DOM Tree（Document Object Model）から生成される、Webページのレイアウト計算のためのツリー構造です。各ノードはLayoutObjectとして表現され、要素の位置、サイズ、スタイル情報を持ちます。

## Layout TreeとCSS適用の関係

HTMLドキュメントがパースされてDOM Treeが構築された後、CSS規則が適用されてLayout Treeが作成されます。この過程で、各要素のスタイル情報（色、サイズ、表示形式など）が計算され、最終的な表示位置が決定されます。

## サンプルLayout Tree構造

以下は、シンプルなHTML文書から生成されるLayout Treeの例です：

```html
<!DOCTYPE html>
<html>
<body>
    <h1 style="color: red;">Hello World</h1>
</body>
</html>
```

```mermaid
graph TD
    A[Document] --> B[html]
    B --> C[body]
    C --> D[h1]
    D --> E["hoge"]
    
    %% スタイル情報を示す
    D -.-> F[("color: red<br/>display: block<br/>font-size: x-large")]
    
    %% ノードタイプの色分け
    classDef documentNode fill:#e1f5fe
    classDef elementNode fill:#f3e5f5
    classDef textNode fill:#e8f5e8
    classDef styleNode fill:#fff3e0
    
    class A documentNode
    class B,C,D elementNode
    class E textNode
    class F styleNode
```

## DOM TreeからLayout Tree構築の段階的な処理

以下のシンプルなHTMLドキュメントを例に、DOM TreeからLayout Treeが構築される過程を段階的に見てみましょう。

```html
<!DOCTYPE html>
<html>
<body>
    <div class="container">
        <h1 style="color: red;">Title</h1>
        <p style="display: none;">Hidden</p>
        <span>Text</span>
    </div>
</body>
</html>
```

### 段階1: DOM Tree構築

最初にHTMLパーサーによってDOM Treeが構築されます。

```mermaid
graph TD
    D[Document] --> H[html]
    H --> B[body]
    B --> DIV[div.container]
    DIV --> H1[h1]
    DIV --> P[p]
    DIV --> SPAN[span]
    H1 --> T1["Title"]
    P --> T2["Hidden"]
    SPAN --> T3["Text"]
    
    %% 属性情報を表示
    H1 -.-> A1[("style='color: red'")]
    P -.-> A2[("style='display: none'")]
    
    classDef domNode fill:#e1f5fe,stroke:#0277bd
    classDef textNode fill:#e8f5e8,stroke:#388e3c
    classDef attrNode fill:#fff3e0,stroke:#f57c00
    
    class D,H,B,DIV,H1,P,SPAN domNode
    class T1,T2,T3 textNode
    class A1,A2 attrNode
```

### 段階2: CSS解析とセレクタマッチング

CSSルールが解析され、各DOM要素とのマッチングが行われます。

```mermaid
graph TD
    subgraph "CSS Rules"
        R1["h1 { font-size: x-large; }"]
        R2["div { display: block; }"]
        R3["span { display: inline; }"]
    end
    
    subgraph "DOM Elements"
        H1[h1]
        DIV[div.container]
        P[p]
        SPAN[span]
    end
    
    R1 -.->|マッチ| H1
    R2 -.->|マッチ| DIV
    R3 -.->|マッチ| SPAN
    
    classDef cssRule fill:#f3e5f5,stroke:#7b1fa2
    classDef domElement fill:#e1f5fe,stroke:#0277bd
    
    class R1,R2,R3 cssRule
    class H1,DIV,P,SPAN domElement
```

### 段階3: Layout Object作成とdisplay:none要素の除外

DOM要素に対応するLayout Objectが作成されますが、`display: none`の要素は除外されます。

```mermaid
graph TD
    subgraph "DOM Tree"
        D1[Document] --> H1[html]
        H1 --> B1[body]
        B1 --> DIV1[div.container]
        DIV1 --> H11[h1]
        DIV1 --> P1[p]
        DIV1 --> SPAN1[span]
        H11 --> T11["Title"]
        P1 --> T21["Hidden"]
        SPAN1 --> T31["Text"]
    end
    
    subgraph "Layout Tree"
        B2[body<br/>Block]
        DIV2[div.container<br/>Block]
        H12[h1<br/>Block]
        SPAN2[span<br/>Inline]
        T12["Title"<br/>Text]
        T32["Text"<br/>Text]
    end
    
    %% 対応関係を示す
    B1 --> B2
    DIV1 --> DIV2
    H11 --> H12
    SPAN1 --> SPAN2
    T11 --> T12
    T31 --> T32
    
    %% display:noneで除外される要素
    P1 -.-x|"display:none<br/>除外"| X[❌]
    T21 -.-x|"除外"| X
    
    %% Layout Treeの構造
    B2 --> DIV2
    DIV2 --> H12
    DIV2 --> SPAN2
    H12 --> T12
    SPAN2 --> T32
    
    classDef domNode fill:#e1f5fe,stroke:#0277bd
    classDef layoutNode fill:#f3e5f5,stroke:#7b1fa2
    classDef textLayoutNode fill:#e8f5e8,stroke:#388e3c
    classDef excluded fill:#ffebee,stroke:#d32f2f
    
    class D1,H1,B1,DIV1,H11,SPAN1 domNode
    class P1 excluded
    class T11,T21,T31 domNode
    class B2,DIV2,H12,SPAN2 layoutNode
    class T12,T32 textLayoutNode
    class X excluded
```

### 段階4: Layout Object種類の決定とスタイル適用

各Layout Objectの種類（Block/Inline/Text）が決定され、スタイル情報が適用されます。

```mermaid
graph TD
    subgraph "Layout Objects with Styles"
        B["body - Block - width: 800px"]
        DIV["div.container - Block - display: block"]
        H1["h1 - Block - color: red - font-size: x-large"]
        SPAN["span - Inline - display: inline"]
        T1["Title - Text - color: red - font-size: x-large"]
        T2["Text - Text - color: black - font-size: medium"]
    end
    
    %% ツリー構造
    B --> DIV
    DIV --> H1
    DIV --> SPAN
    H1 --> T1
    SPAN --> T2
    
    %% スタイル継承を表示
    H1 -.->|継承| T1
    
    classDef blockNode fill:#e3f2fd,stroke:#1976d2
    classDef inlineNode fill:#f3e5f5,stroke:#7b1fa2
    classDef textNode fill:#e8f5e8,stroke:#388e3c
    
    class B,DIV,H1 blockNode
    class SPAN inlineNode
    class T1,T2 textNode
```

### 段階5: サイズ計算 (computeSize)

親から子へと再帰的にサイズが計算されます。

```mermaid
graph TD
    subgraph "Size Calculation"
        B["body - Block - width: 800px - height: 120px"]
        DIV["div.container - Block - width: 800px - height: 120px"]
        H1["h1 - Block - width: 800px - height: 60px"]
        SPAN["span - Inline - width: 40px - height: 20px"]
        T1["Title - Text - width: 100px - height: 60px"]
        T2["Text - Text - width: 40px - height: 20px"]
    end
    
    %% サイズ計算の流れ
    B --> DIV
    DIV --> H1
    DIV --> SPAN
    H1 --> T1
    SPAN --> T2
    
    %% 計算順序を表示
    B -.->|"1. 親サイズ設定"| DIV
    DIV -.->|"2. 子サイズ計算"| H1
    DIV -.->|"3. 子サイズ計算"| SPAN
    H1 -.->|"4. テキストサイズ"| T1
    SPAN -.->|"5. テキストサイズ"| T2
    
    classDef blockNode fill:#e3f2fd,stroke:#1976d2
    classDef inlineNode fill:#f3e5f5,stroke:#7b1fa2
    classDef textNode fill:#e8f5e8,stroke:#388e3c
    
    class B,DIV,H1 blockNode
    class SPAN inlineNode
    class T1,T2 textNode
```

### 段階6: 位置計算 (computePosition)

各Layout Objectの画面上の位置が計算されます。

```mermaid
graph TD
    subgraph "Position Calculation"
        B["body - Block - x: 0, y: 0 - 800×120"]
        DIV["div.container - Block - x: 0, y: 0 - 800×120"]
        H1["h1 - Block - x: 0, y: 0 - 800×60"]
        SPAN["span - Inline - x: 0, y: 60 - 40×20"]
        T1["Title - Text - x: 0, y: 0 - 100×60"]
        T2["Text - Text - x: 0, y: 60 - 40×20"]
    end
    
    %% レイアウト構造
    B --> DIV
    DIV --> H1
    DIV --> SPAN
    H1 --> T1
    SPAN --> T2
    
    %% 位置関係を表示
    H1 -.->|"縦方向配置"| SPAN
    
    classDef blockNode fill:#e3f2fd,stroke:#1976d2
    classDef inlineNode fill:#f3e5f5,stroke:#7b1fa2
    classDef textNode fill:#e8f5e8,stroke:#388e3c
    
    class B,DIV,H1 blockNode
    class SPAN inlineNode
    class T1,T2 textNode
```

### 段階7: 最終的なLayout Tree

最終的に構築されたLayout Treeには、描画に必要な全ての情報が含まれています。

```mermaid
graph TD
    B["body - LayoutObjectKind::Block - Position: (0,0) - Size: 800×120 - Style: default"]
    
    DIV["div.container - LayoutObjectKind::Block - Position: (0,0) - Size: 800×120 - Style: display:block"]
    
    H1["h1 - LayoutObjectKind::Block - Position: (0,0) - Size: 800×60 - Style: color:red, font-size:x-large"]
    
    SPAN["span - LayoutObjectKind::Inline - Position: (0,60) - Size: 40×20 - Style: display:inline"]
    
    T1["Title - LayoutObjectKind::Text - Position: (0,0) - Size: 100×60 - Style: color:red, font-size:x-large"]
    
    T2["Text - LayoutObjectKind::Text - Position: (0,60) - Size: 40×20 - Style: color:black, font-size:medium"]
    
    %% ツリー構造
    B --> DIV
    DIV --> H1
    DIV --> SPAN
    H1 --> T1
    SPAN --> T2
    
    classDef blockNode fill:#e3f2fd,stroke:#1976d2
    classDef inlineNode fill:#f3e5f5,stroke:#7b1fa2
    classDef textNode fill:#e8f5e8,stroke:#388e3c
    
    class B,DIV,H1 blockNode
    class SPAN inlineNode
    class T1,T2 textNode
```

## 重要なポイント

### 1. `display: none`要素の除外
- DOM Treeには存在するが、Layout Treeには含まれない
- 子要素も一緒に除外される
- レンダリングの最適化に寄与

### 2. Layout Object種類の決定
- **Block要素**: 縦方向に積み重なる（`<div>`, `<h1>`, `<p>`など）
- **Inline要素**: 横方向に並ぶ（`<span>`, `<a>`など）
- **Text要素**: 実際のテキスト内容を描画

### 3. スタイル継承とカスケーディング
- 親要素のスタイルが子要素に継承される
- インラインスタイル > CSS規則の優先順位
- `ComputedStyle`で最終的なスタイル値を管理

### 4. レイアウト計算の順序
1. **サイズ計算**: 親から子へとサイズを決定
2. **位置計算**: ブロック要素は縦方向、インライン要素は横方向に配置
3. **描画準備**: `DisplayItem`生成の準備完了

このようにして、DOM TreeからLayout Treeへの変換が段階的に行われ、最終的にWebページのレンダリングに必要な全ての情報が整います。

## 実際のLayout Treeノード詳細情報

実際のLayoutObjectが持つ具体的な情報を含むLayout Treeの構造を詳細に図示します。

### 例: シンプルなHTML文書のLayout Tree

```html
<!DOCTYPE html>
<html>
<body>
    <div class="container" style="background-color: #f0f0f0;">
        <h1 style="color: red; font-size: x-large;">Hello World</h1>
        <p style="display: none;">Hidden Text</p>
    </div>
</body>
</html>
```

### Layout Tree with Node Information (Overview)

```mermaid
graph TD
    %% Root Layout Object
    BODY["🔧 body<br/>kind: Block<br/>style: default<br/>position: (0,0)<br/>size: 800×120"]
    
    %% Container Div
    DIV["🔧 div.container<br/>kind: Block<br/>style: background=#f0f0f0<br/>position: (0,0)<br/>size: 800×120"]
    
    %% H1 Element
    H1["🔧 h1<br/>kind: Block<br/>style: color=red, font=x-large<br/>position: (0,0)<br/>size: 800×120"]
    
    %% H1 Text Node
    H1_TEXT["📝 'Hello World'<br/>kind: Text<br/>style: inherited from h1<br/>position: (0,0)<br/>size: 220×120"]
    
    %% 除外された要素（参考）
    EXCLUDED["❌ p要素 (display: none)<br/>Layout Treeから除外"]
    
    %% ツリー構造の関係
    BODY --> DIV
    DIV --> H1
    H1 --> H1_TEXT
    
    %% 除外要素の表示（破線）
    DIV -.-x EXCLUDED
    
    %% スタイルの分類
    classDef blockElement fill:#e3f2fd,stroke:#1976d2,stroke-width:2px
    classDef textElement fill:#e8f5e8,stroke:#388e3c,stroke-width:2px
    classDef excludedElement fill:#ffebee,stroke:#d32f2f,stroke-width:2px,stroke-dasharray: 5 5
    
    class BODY,DIV,H1 blockElement
    class H1_TEXT textElement
    class EXCLUDED excludedElement
```

### LayoutObjectが持つ主要情報

```mermaid
graph LR
    subgraph "LayoutObject"
        A["kind<br/>(Block/Inline/Text)"]
        B["style<br/>(色・フォント・表示方法)"]
        C["position<br/>(x, y座標)"]
        D["size<br/>(幅・高さ)"]
        E["tree pointers<br/>(親・子・兄弟への参照)"]
    end
    
    classDef info fill:#f0f8ff,stroke:#4169e1,stroke-width:2px
    class A,B,C,D,E info
```

#### LayoutObjectの核となる5つの情報

1. **kind** - レイアウトタイプ（Block/Inline/Text）
2. **style** - 計算済みスタイル（色、フォント、背景色など）
3. **position** - 画面上の位置（x, y座標）
4. **size** - 要素のサイズ（幅、高さ）
5. **tree pointers** - ツリー構造の参照（親、子、兄弟ノード）

### 構築処理の概要フロー

```mermaid
graph TD
    DOM["DOM Node"] --> LAYOUT["LayoutObject作成"]
    CSS["CSS Rules"] --> LAYOUT
    LAYOUT --> STYLE["スタイル適用"]
    STYLE --> SIZE["サイズ計算"]
    SIZE --> POS["位置計算"]
    POS --> PAINT["描画準備完了"]
    
    classDef process fill:#e8f4fd,stroke:#2196f3,stroke-width:2px
    class DOM,LAYOUT,STYLE,SIZE,POS,PAINT process
```



## Layout Objectの種類

Layout Treeの各ノードは、以下のいずれかの種類に分類されます：

### 1. Block Layout Object
- **特徴**: ブロックレベル要素（`<div>`, `<p>`, `<h1>`など）
- **レイアウト**: 縦方向に積み重なる
- **幅**: 親要素の幅いっぱいに広がる

### 2. Inline Layout Object  
- **特徴**: インライン要素（`<span>`, `<a>`など）
- **レイアウト**: 横方向に並ぶ
- **幅**: コンテンツの幅に合わせる

### 3. Text Layout Object
- **特徴**: テキストノード
- **レイアウト**: 文字列の描画を担当
- **サイズ**: フォントサイズに基づいて計算

## レイアウト計算の流れ

```mermaid
graph LR
    A[DOM Tree] --> B[Style計算]
    B --> C[Layout Tree構築]
    C --> D[サイズ計算]
    D --> E[位置計算]
    E --> F[描画]
    
    style A fill:#e1f5fe
    style F fill:#e8f5e8
    style B,C,D,E fill:#f3e5f5
```

### 1. Style計算 (cascadingStyle)
CSS規則をDOM要素に適用し、最終的なスタイル値を決定します。

### 2. Layout Tree構築
DOM TreeからLayout Treeを生成し、`display: none`の要素などは除外されます。

### 3. サイズ計算 (computeSize)
各Layout Objectの幅と高さを計算します。親から子へと再帰的に処理されます。

### 4. 位置計算 (computePosition)
各Layout Objectの画面上の位置（x, y座標）を計算します。

### 5. 描画 (paint)
Layout Objectの情報を基に、実際の描画命令（DisplayItem）を生成します。

## より複雑なLayout Tree例

```mermaid
graph TD
    Doc[Document] --> Html[html]
    Html --> Body[body]
    Body --> Header[header]
    Body --> Main[main]
    Body --> Footer[footer]
    
    Header --> H1[h1]
    H1 --> T1["Welcome"]
    
    Main --> Div1[div.container]
    Div1 --> P1[p]
    Div1 --> P2[p]
    
    P1 --> T2["First paragraph"]
    P2 --> Span1[span.highlight]
    P2 --> T3[" continues here"]
    Span1 --> T4["Important text"]
    
    Footer --> P3[p]
    P3 --> T5["Footer content"]
    
    %% スタイル適用を表示
    H1 -.-> S1[("color: blue<br/>font-size: xx-large")]
    Span1 -.-> S2[("background-color: yellow<br/>color: black")]
    
    %% ノードタイプの色分け
    classDef documentNode fill:#e1f5fe
    classDef elementNode fill:#f3e5f5
    classDef textNode fill:#e8f5e8
    classDef styleNode fill:#fff3e0
    
    class Doc documentNode
    class Html,Body,Header,Main,Footer,H1,Div1,P1,P2,P3,Span1 elementNode
    class T1,T2,T3,T4,T5 textNode
    class S1,S2 styleNode
```

## LayoutObjectクラスの主要メソッド

| メソッド | 説明 |
|----------|------|
| `paint()` | 描画命令（DisplayItem）を生成 |
| `computeSize()` | サイズを計算 |
| `computePosition()` | 位置を計算 |
| `cascadingStyle()` | CSS規則を適用 |
| `updateKind()` | Layout Objectの種類を更新 |
| `isNodeSelected()` | CSSセレクタにマッチするかチェック |

## 実装上の特徴

- **ツリー構造**: 親子関係（`parent`, `firstChild`）と兄弟関係（`nextSibling`）で構成
- **スタイル情報**: `ComputedStyle`オブジェクトで管理
- **位置・サイズ**: `LayoutPoint`と`LayoutSize`で座標とサイズを管理
- **描画最適化**: `display: none`の要素は描画をスキップ

このLayout Tree構造により、Webページの効率的なレンダリングが可能になります。
